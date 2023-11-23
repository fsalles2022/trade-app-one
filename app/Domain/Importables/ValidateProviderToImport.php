<?php

namespace TradeAppOne\Domain\Importables;

use ClaroBR\Components\ClaroBRDomains;
use NextelBR\Enumerators\NextelBRConstants;
use TimBR\Components\TimBRDomains;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Facades\Uniqid;

class ValidateProviderToImport
{
    const NEXTEL_COD = 'nextelCod';
    const NEXTEL_REF = 'nextelRef';
    const CLARO      = 'claro';
    const TIM        = 'tim';
    const OI         = 'oi';

    protected $providers = [
        Operations::CLARO  => self::CLARO,
        Operations::TIM    => self::TIM,
        Operations::OI     => self::OI,
        Operations::NEXTEL => [self::NEXTEL_COD, self::NEXTEL_REF]
    ];

    protected $network;
    protected $pointOfSale;
    protected $line;

    public function __construct(Network $network, array $line = [], ?PointOfSale $pointOfSale = null)
    {
        $this->line        = $line;
        $this->network     = $network;
        $this->pointOfSale = $pointOfSale;
    }

    public function make(): ?string
    {
        $identifiers = [];

        foreach (array_keys($this->providers) as $operation) {
            $identifiers[$operation] = $this->$operation();
        }

        $identifiers = array_filter($identifiers);

        return filled($identifiers)
            ? json_encode($identifiers)
            : null;
    }

    private function claro(): ?string
    {
        $provider = $this->pointOfSale === null
            ? $this->createToClaro()
            : $this->updateToClaro();

        return filled($provider)
            ? ClaroBRDomains::formatPointOfSaleIdentifier($provider)
            : null;
    }

    private function tim(): ?string
    {
        return isset($this->line[self::TIM]) && filled($this->line[self::TIM])
            ? TimBRDomains::formatPointOfSaleIdentifier($this->line[self::TIM])
            : null;
    }

    private function nextel(): array
    {
        return array_filter([
            NextelBRConstants::POINT_OF_SALE_COD => data_get($this->line, self::NEXTEL_COD),
            NextelBRConstants::POINT_OF_SALE_REF => data_get($this->line, self::NEXTEL_REF)
        ]);
    }

    private function oi(): ?string
    {
        return data_get($this->line, self::OI);
    }

    private function updateToClaro(): ?string
    {
        if ($this->network->isMasterDealer()) {
            return $this->pointOfSale->providerIdentifiers[Operations::CLARO] ?? $this->generateCustCode();
        }

        return data_get($this->line, self::CLARO);
    }

    private function createToClaro(): ?string
    {
        if ($this->network->isMasterDealer()) {
            return $this->generateCustCode();
        }

        return data_get($this->line, self::CLARO);
    }

    public function generateCustCode(): string
    {
        return strtoupper($this->network->slug . '-' . substr(Uniqid::generate(), 5, 4));
    }
}
