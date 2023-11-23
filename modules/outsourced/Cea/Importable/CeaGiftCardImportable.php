<?php

namespace Outsourced\Cea\Importable;

use League\Csv\Writer;
use Outsourced\Cea\Hooks\CeaHooks;
use Outsourced\Cea\Models\CeaGiftCard;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Importables\ImportableInterface;

class CeaGiftCardImportable implements ImportableInterface
{
    protected $codes = [];

    private const TRIANGULATION = 'TRIANGULATION';
    private const TRADE_IN      = 'TRADE_IN';

    private const PARTNER_CODES_MATCH = [
        self::TRIANGULATION => CeaHooks::PARTNER_TRIANGULATION,
        self::TRADE_IN      => CeaHooks::PARTNER_TRADE_IN,
    ];

    public function getColumns(): array
    {
        return [
            'codes' => 'codes',
            'partner' => 'partner'
        ];
    }

    public function getExample(): Writer
    {
        $header = $this->getColumns();
        $data   = [$header, ['123456',self::TRIANGULATION], ['654321',self::TRADE_IN]];

        return CsvHelper::arrayToCsv($data);
    }

    public function processLine($line)
    {
        $code        = trim(data_get($line, 'codes'));
        $partnerType = trim(data_get($line, 'partner'));

        if ($this->isLineInvalid($code, $partnerType)) {
            return;
        }

        $this->saveGiftCard($code, self::PARTNER_CODES_MATCH[$partnerType]);
    }

    private function saveGiftCard(string $code, int $partner)
    {
        return CeaGiftCard::query()->create(['code' => $code, 'partner' => $partner]);
    }

    public function getType(): string
    {
        return Importables::CEA_GIFT_CARDS;
    }

    private function isLineInvalid($code, $partnerType): bool
    {
        return (empty($code) || empty($partnerType) || ! isset(self::PARTNER_CODES_MATCH[$partnerType]) );
    }
}
