<?php

namespace TradeAppOne\Domain\Importables;

use Exception;
use TradeAppOne\Domain\Components\Helpers\ImportableHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\ImportableExceptions;
use TradeAppOne\Facades\UserPolicies;

class DeviceOutSourcedImportable implements ImportableInterface
{
    public function getExample($networkSlug): array
    {
        return [
            'HJ56BN5D',
            'Galaxy S10 Plus',
            'Samsung Galaxy S10 Plus Branco 128GB',
            'Samsung',
            'Branco',
            '128GB',
            '3323.20',
            $networkSlug
        ];
    }

    public function getExportData($networkId):array
    {
        $devices = DeviceOutSourced::query()
            ->where('networkId', $networkId)
            ->with('network')
            ->get(['sku', 'label', 'model', 'brand', 'color', 'storage', 'price', 'networkId']);

        return $devices->map(static function (DeviceOutSourced $device) {
            $slug                = $device->network->slug;
            $device->networkSlug = $slug;
            unset($device->network, $device->networkId);
            return $device;
        })->toArray();
    }

    public function processLine($line)
    {
        ImportableHelper::hasErrorInLine($line, $this->rules(), $this->getColumns());
        try {
            $network = UserPolicies::hasAuthorizationUnderNetwork($line['networkSlug'])
                ->getNetworksAuthorized()
                ->where('slug', $line['networkSlug'])
                ->first();

            $line['networkId'] = $network->id;
            return DeviceOutSourced::updateOrCreate(
                [
                    'sku'       => $line['sku'],
                    'networkId' => $line['networkId']
                ],
                $line
            );
        } catch (Exception $ex) {
            throw ImportableExceptions::userCanNotAddToNetwork();
        }
    }

    private function rules(): array
    {
        return [
            'sku'         => 'required|String',
            'label'       => 'required|String',
            'model'       => 'required|String',
            'brand'       => 'sometimes|String',
            'color'       => 'sometimes|String',
            'storage'     => 'sometimes|String',
            'price'       => 'required|numeric|min:0.01|max:99999999', //TODO: change min to gt when laravel updated
            'networkSlug' => 'required|string|exists:networks,slug'
        ];
    }

    public function getColumns(): array
    {
        return [
            'sku'         => 'identificadorDoDispositivo',
            'model'       => 'modelo',
            'label'       => 'nomeComercial',
            'brand'       => 'marca',
            'color'       => 'cor',
            'storage'     => 'armazenamento',
            'price'       => 'preco',
            'networkSlug' => 'rede'
        ];
    }

    public function getType(): string
    {
        return Importables::DEVICES_OUTSOURCED;
    }
}
