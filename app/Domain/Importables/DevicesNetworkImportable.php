<?php

namespace TradeAppOne\Domain\Importables;

use Buyback\Models\DevicesNetwork;
use TradeAppOne\Domain\Components\Helpers\ImportableHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableInterface;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\ImportableExceptions;

class DevicesNetworkImportable implements ImportableInterface
{
    public function getExample($networkId)
    {
        return [
            '1',
            'HJ56BN5D',
            $networkId
        ];
    }

    public function getColumns()
    {
        return [
            "deviceId" => 'identificadorDoDispositivo',
            "sku" => 'sku',
            "networkId" => 'identificadorDaRede',
        ];
    }

    public function processLine($line)
    {
        ImportableHelper::hasErrorInLine($line, $this->rules(), $this->getColumns());

        $networkId = (int) $line['networkId'];
        $deviceId  = (int) $line['deviceId'];

        $deviceNetwork = DevicesNetwork::query()
            ->where('networkId', $networkId)
            ->where('deviceId', $deviceId)
            ->first();

        if ($deviceNetwork) {
            throw ImportableExceptions::registerAlreadyExists();
        }

        $canAttachDeviceToNetwork = $this->getUser()->hasAuthorityUnderNetwork($networkId);
        if ($canAttachDeviceToNetwork) {
            return DevicesNetwork::query()->create($line);
        }

        throw ImportableExceptions::userCanNotAddToNetwork();
    }

    private function getUser(): User
    {
        return auth()->user();
    }

    private function rules(): array
    {
        return [
            'networkId' => 'required|int|exists:networks,id',
            'deviceId' => 'required|int|exists:devices,id'
        ];
    }

    public function getType()
    {
        return Importables::DEVICES_NETWORK;
    }
}
