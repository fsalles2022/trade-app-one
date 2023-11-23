<?php

declare(strict_types=1);

namespace Bulletin\Repositories;

use Bulletin\Models\Bulletin;
use Bulletin\Service\BulletinFileServer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Policies\Authorizations;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as AliasCollection;

class BulletinRepository
{
    /** @var Authorizations */
    private $authorizations;

    /** @var Bulletin */
    private static $bulletin;

    /** @var Network */
    private static $network;

    public function __construct(Authorizations $authorizations)
    {
        $this->authorizations = $authorizations;
    }

    /** @return Builder */
    public function getAll(): Builder
    {
        $pointOfSaleIds = $this->authorizations->getPointsOfSaleAuthorized()->pluck('id');

        return Bulletin::whereHas('pointOfSale', static function (Builder $pointOfSale) use ($pointOfSaleIds) {
            $pointOfSale->whereIn('pointOfSaleId', $pointOfSaleIds);
        })->where('finalDate', '>=', Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * @param Mixed[] $attributes
     * @return Mixed[]
     */
    public function save(array $attributes): array
    {
        $payload   = json_decode(data_get($attributes, 'data', ''), true);
        $file      = $attributes['imageDesktop'] ?? '';
        $fileName  = BulletinFileServer::getFileName($attributes);
        $bulletins = [];

        foreach (($payload['networks'] ?? []) as $network) {
            $roleSlugs         = array_column($network['roles'], 'slug') ?? [];
            $networkSlug       = $network['network']['slug'] ?? '';
            $pointOfSalesSlugs = array_column($network['pointOfSales'], 'slug') ?? [];

            self::$network = Network::where('slug', $networkSlug)->first();

            $path    = BulletinFileServer::save(self::$network, $file, $fileName);
            $urlPath = Storage::disk('s3')->url($path);

            $payload['imageDesktop'] = $urlPath;

            self::$bulletin = Bulletin::firstOrCreate(self::adapter($payload));

            $this->syncRoles($roleSlugs);
            $this->syncPointOfSales($pointOfSalesSlugs);

            $bulletins[] = self::$bulletin;
        }

        return $bulletins;
    }

    /**
     * @param Mixed[] $attributes
     * @param Bulletin $bulletin
     * @return bool
     */
    public function update(array $attributes, Bulletin $bulletin): bool
    {
        if ($file = ($attributes['imageDesktop'] ?? null)) {
            $fileName = BulletinFileServer::getFileName($attributes);
            $path     = BulletinFileServer::update($bulletin, $file, $fileName);
            $urlPath  = Storage::disk('s3')->url($path);

            $attributes['urlImage'] = $urlPath;
        }

        return $bulletin->update($attributes);
    }

    /**
     * @param Bulletin $bulletin
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Bulletin $bulletin): ?bool
    {
        BulletinFileServer::delete($bulletin);
        return $bulletin->delete();
    }

    /**
     * @param Mixed[] $attributes
     * @param Bulletin $bulletin
     * @return bool
     */
    public function updateStatus(array $attributes, Bulletin $bulletin): bool
    {
        return $bulletin->update($attributes);
    }

    /** @param Mixed $roleSlugs */
    private function syncRoles(array $roleSlugs): void
    {
        $roles = Role::where('networkId', self::$network->id)->whereIn('slug', $roleSlugs);
        self::$bulletin->role()->sync($roles->pluck('id'));
    }

    /** @param Mixed $pointOfSaleSlugs */
    private function syncPointOfSales(array $pointOfSaleSlugs): void
    {
        $pointOfSales = PointOfSale::where('networkId', self::$network->id)->whereIn('slug', $pointOfSaleSlugs);
        self::$bulletin->pointOfSale()->sync($pointOfSales->pluck('id'));
    }

    /**
     * @param Mixed[] $attributes
     * @return Mixed[]
     */
    private static function adapter(array $attributes): array
    {
        return [
            'title' =>  $attributes['title'] ?? null,
            'description' =>  $attributes['description'] ?? null,
            'networkId' => self::$network->id,
            'status' =>  (bool) ($attributes['status'] ?? true),
            'urlImage' =>  $attributes['imageDesktop'] ?? null,
            'initialDate' =>  $attributes['period']['startDate'] ?? null,
            'finalDate' =>  $attributes['period']['endDate'] ?? null
        ];
    }

    public function getAuthorizedStructureByUserAuth(): Collection
    {
        $pointOfSales = $this->authorizations->getPointsOfSaleAuthorized();
        $network      = $this->authorizations->getNetworksAuthorized();
        $roles        = $this->authorizations->getRolesAuthorized();

        return $network->transform(function ($network) use ($pointOfSales, $roles) {
            $pointOfSalesFormatted = $pointOfSales->where('networkId', $network->id)->transform(static function ($pointOfSale) {

                return  [
                    'slug' => $pointOfSale->slug,
                    'label' => $pointOfSale->label,
                    'networkSlug' => $pointOfSale->network->slug
                ];
            });

            $rolesFormatted = $roles->where('networkId', $network->id)->transform(static function ($role) {
                return  [
                    'slug' => $role->slug,
                    'label' => $role->name,
                    'networkSlug' => $role->network->slug
                ];
            });

            return [
                'networks' => [
                    'network' => [
                        'slug' => $network->slug,
                        'label' => $network->label,
                        'pointOfSales' => array_values($pointOfSalesFormatted->toArray()),
                        'roles' => array_values($rolesFormatted->toArray())
                    ]
                ]
            ];
        });
    }

    public function getActiveUserBulletins(): AliasCollection
    {
        $user = $this->authorizations->getUser();
        $now  = Carbon::now()->format('Y-m-d H:i:s');

        return Bulletin::where('status', true)
            ->where('finalDate', '>=', $now)
            ->whereHas('role', function (Builder $query) use ($user) {
                $query->where('roles.id', $user->roleId);
            })
            ->whereHas('pointOfSale', function (Builder $query) use ($user) {
                $query->whereHas('users', function (Builder $query) use ($user) {
                    $query->where('users.id', $user->id);
                });
            })
            ->whereDoesntHave('user', function (Builder $query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get();
    }

    /** @param Bulletin $bulletin */
    public function updateBulletinsUsers(Bulletin $bulletin): void
    {
        $bulletin
            ->user()
            ->attach([$bulletin->id => ['seen' => true, 'userId' =>$this->authorizations->getUser()->id]]);
    }
}
