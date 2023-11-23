<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Importables;

use Discount\Models\DeviceTim;
use Discount\Models\DiscountProductTim;
use Discount\Services\DeviceTimService;
use Discount\Services\DiscountProductDeviceTimService;
use Discount\Services\DiscountProductTimService;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Importables;

class TimBRRebateImportable implements ImportableInterface
{
    private const TIM_PRODUCTS_INDEX_START = 5;

    /** @var DeviceTimService */
    private $deviceTimService;

    /** @var DiscountProductTimService */
    private $discountProductTimService;

    /** @var DiscountProductDeviceTimService */
    private $discountProductDeviceTimService;

    /** @var DiscountProductTim[] */
    private $products = [];

    /** @var DeviceTim[] */
    private $devices = [];

    /** @var string[] */
    private $timProductsToImport = [];

    /** @var array[] */
    private $deviceProductsProcessedToImport = [];

    public function __construct(
        DeviceTimService $deviceTimService,
        DiscountProductTimService $discountProductTimService,
        DiscountProductDeviceTimService $discountProductDeviceTimService
    ) {
        $this->deviceTimService                = $deviceTimService;
        $this->discountProductTimService       = $discountProductTimService;
        $this->discountProductDeviceTimService = $discountProductDeviceTimService;

        $this->mountDevicesList();
        $this->mountProductsList();
    }

    public function getExample(): array
    {
        return [
            [],
            [],
            [],
            [],
            ["","","","","","1-22AAAA","1-12BBBB","1-23CCCC","1-44DDDD"],
            [],
            [],
            ["FABRICANTE","CÓDIGO","DESCRIÇÃO COMERCIAL","DESCRIÇÃO SAP","PREÇO AVULSO APARELHO","TIM Black A 5","TIM Black B 5","TIM Black C Hero 5 0","TIM Black C Ultra 5"],
            ["APPLE",12345,"APPLE IPHONE 14 PLUS 128GB","APPLE IPHONE 14 PLUS 128GB-AZUL",6200,100,150,200,250],
            ["APPLE",22345,"APPLE IPHONE 14 PLUS 128GB","APPLE IPHONE 14 PLUS 128GB-BRANCO",6200,100,150,200,250],
        ];
    }

    public function getColumns(): array
    {
        return [
            'externalCode'      => trans('importables.timRebate.externalCode'),
            'brand'             => trans('importables.timRebate.brand'),
            'model'             => trans('importables.timRebate.model'),
            'commercialModel'   => trans('importables.timRebate.commercialModel'),
            'price'             => trans('importables.timRebate.price'),
        ];
    }

    public function startProcess(): void
    {
        DB::beginTransaction();
    }

    public function endProcessWithSuccess(): void
    {
        $this->dispatchToInsert();

        DB::commit();
    }

    public function endProcessWithErrors(): void
    {
        DB::rollBack();
    }

    public function processLine($line): void
    {
        if ($this->validateIsEmptyLine($line) || $this->isHeaderLine($line)) {
            return;
        }

        $line = $this->aggregateTimProductPriceMatchInLine($line);

        $device   = $this->processDevice($line);
        $products = $this->processProducts($line);

        $this->mountDeviceProducts($device, $products);
    }

    /** @param mixed[] $line */
    private function validateIsEmptyLine(array $line): bool
    {
        unset($line['extra_columns']);

        $valuesFiltered = array_filter(
            array_values($line)
        );

        return empty($valuesFiltered);
    }

    /** @param mixed[] $line */
    private function isHeaderLine(array $line): bool
    {
        unset($line['extra_columns']);

        return array_diff($line, $this->getColumns()) === array_diff($this->getColumns(), $line);
    }

    /**
     * Combine timProducts external code with name and price by line
     *
     * @param mixed[] $line
     * @return mixed[]
     */
    private function aggregateTimProductPriceMatchInLine(array $line): array
    {
        $extraColumnsWithPrice              = $line['extra_columns'];
        $timProductsWithCodeAndNameAndPrice = [];

        $timProductsPrices = array_values($extraColumnsWithPrice);
        $timProductsNames  = array_keys($extraColumnsWithPrice);

        foreach ($timProductsPrices as $index => $productPrice) {
            $productName         = $timProductsNames[$index] ?? null;
            $productExternalCode = $this->timProductsToImport[$index + self::TIM_PRODUCTS_INDEX_START] ?? null;

            if (empty($productPrice)) {
                continue;
            }

            $timProductsWithCodeAndNameAndPrice[] = [
                'name' => $productName,
                'price' => $productPrice,
                'externalCode' => $productExternalCode,
            ];
        }

        $line['timProductsMatched'] = $timProductsWithCodeAndNameAndPrice;

        return $line;
    }

    private function processDevice(array $line): DeviceTim
    {
        $device = $this->getDevice($line['externalCode']);

        if ($device instanceof DeviceTim) {
            return $this->deviceTimService->updatePrice($device, (float) $line['price']);
        }

        return $this->deviceTimService->create([
            'label'                 => $line['model'],
            'model'                 => $line['model'],
            'brand'                 => $line['brand'],
            'price'                 => (float) $line['price'],
            'externalIdentifier'    => $line['externalCode'],
        ]);
    }

    /**
     * @param mixed[] $line
     * @return DiscountProductTim[]
     */
    private function processProducts(array $line): array
    {
        $timProductsMatched = $line['timProductsMatched'] ?? [];
        $products           = [];

        foreach ($timProductsMatched as $timProductMatched) {
            $product = $this->getProduct($timProductMatched['externalCode']);

            if ($product instanceof DiscountProductTim) {
                $products[] = [
                    'discountProductTim' => $product,
                    'productLine'        => $timProductMatched,
                ];

                continue;
            }

            $products[] = [
                'discountProductTim' => $this->createProduct($timProductMatched),
                'productLine'        => $timProductMatched,
            ];
        }

        return $products;
    }

    private function createProduct(array $timProduct): DiscountProductTim
    {
        if (empty($timProduct['name'] ?? '') || empty($timProduct['externalCode'] ?? '')) {
            throw new \InvalidArgumentException(trans('timBR::exceptions.TimBRRebateImport.invalid_product'));
        }

        $product = $this->discountProductTimService->create([
            'label'              => $timProduct['name'] ?? '',
            'externalIdentifier' => $timProduct['externalCode'] ?? '',
        ]);

        $this->products[$product->externalIdentifier] = $product;

        return $product;
    }

    /** Mount indexed devices list */
    private function mountDevicesList(): void
    {
        $devices = $this->deviceTimService->getAll();

        foreach ($devices as $device) {
            $this->devices[$device->externalIdentifier] = $device;
        }
    }

    private function getDevice(string $externalCode): ?DeviceTim
    {
        return $this->devices[$externalCode] ?? null;
    }

    /** Mount indexed products list */
    private function mountProductsList(): void
    {
        $products = $this->discountProductTimService->getAll();

        foreach ($products as $product) {
            $this->products[$product->externalIdentifier] = $product;
        }
    }

    private function getProduct(string $externalCode): ?DiscountProductTim
    {
        return $this->products[$externalCode] ?? null;
    }

    /** @param mixed[] $products */
    private function mountDeviceProducts(DeviceTim $device, array $products): void
    {
        foreach ($products as $product) {
            $this->deviceProductsProcessedToImport[] = [
                'discount'          => (float) $product['productLine']['price'],
                'discountProductId' => $product['discountProductTim']->id,
                'deviceId'          => $device->id,
            ];
        }
    }

    private function dispatchToInsert(): void
    {
        $this->discountProductDeviceTimService->deleteAll();
        $this->discountProductDeviceTimService->createInBulk($this->deviceProductsProcessedToImport);
    }

    /** @param mixed[] $timProductsToImport */
    public function setTimProductToImport(array $timProductsToImport): void
    {
        $this->timProductsToImport = $timProductsToImport;
    }

    public function getType(): string
    {
        return Importables::TIM_REBATE;
    }

    public static function buildExample(): Writer
    {
        /** @var TimBRRebateImportable $timBrRebateImportable */
        $timBrRebateImportable = resolve(__CLASS__);
        return CsvHelper::arrayToCsv($timBrRebateImportable->getExample());
    }
}
