<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Importables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\Options;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleExceptions;

class ServicesImportable implements ImportableInterface
{
    /*** @var PointOfSale */
    private static $pointOfSale;

    /*** @var AvailableService */
    private static $availableServices;

    public function processLine($line): void
    {
        $value               = (bool) Arr::get($line, 'value');
        $action              = Arr::get($line, 'action');
        $providerIdentifiers = Arr::get($line, 'providerIdentifiers');

        self::$pointOfSale       = PointOfSaleRepository::findByProviderIdentifiers($providerIdentifiers)->first();
        self::$availableServices = self::$pointOfSale->availableServicesRelation();

        self::builder($action)->get()
            ->each(static function (AvailableService $availableServiceBuilder) use ($value, $action) {
                $serviceOption = ServiceOption::findByAction($action);
                $method        = $value ? 'sync' : 'detach';

                $availableServiceBuilder->options()->{$method}($serviceOption);
            });
    }

    private static function builder(string $action): Builder
    {
        $operations = Options::getOperationsByOptions($action, Options::AUTENTICA_RELATION);

        return self::$availableServices->get()->isEmpty()
            ? self::createAvailableServicesPointOfSaleOfNetwork($operations)
            : AvailableService::findByPointOfSale(self::$pointOfSale, $operations);
    }

    private static function createAvailableServicesPointOfSaleOfNetwork(array $operations): Builder
    {
        $networkAvailableServices = AvailableService::findByNetwork(self::$pointOfSale->network, $operations)->get();
        throw_if($networkAvailableServices->isEmpty(), PointOfSaleExceptions::notFoundPointOfSaleNetwork());

        $collection = self::$availableServices->orWhere('networkId', '=', self::$pointOfSale->network->id)->get();

        $collection->each(static function (AvailableService $availableService) {
            $newAvailableServices = AvailableService::create([
                'serviceId' => $availableService->serviceId,
                'pointOfSaleId' => self::$pointOfSale->id
            ]);

            $serviceOptionsCollection = $availableService->options()->get();

            $serviceOptionsCollection->isEmpty()
                ?: self::attachOptions($newAvailableServices, $serviceOptionsCollection);
        });

        return AvailableService::findByPointOfSale(self::$pointOfSale, $operations);
    }

    private static function attachOptions(AvailableService $availableService, Collection $collection): void
    {
        $collection->each(static function (ServiceOption $serviceOption) use ($availableService) {
            $availableService->options()->attach($serviceOption->id);
        });
    }

    public function getColumns(): array
    {
        return [
            'providerIdentifiers' => 'Codigo Pdv',
            'action' => 'Opcao',
            'value' => 'Valor',
        ];
    }

    public function getExample(): array
    {
        return ['XPTO', 'CARTEIRIZACAO', 1];
    }

    public static function buildExample(): Writer
    {
        $instance = (new self());

        return CsvHelper::arrayToCsv([
            $instance->getColumns(),
            $instance->getExample()
        ]);
    }

    public function getType(): string
    {
        return Importables::SERVICES;
    }
}
