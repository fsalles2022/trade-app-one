<?php

namespace TradeAppOne\Domain\Importables;

use NextelBR\Enumerators\NextelBRConstants;
use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Components\Helpers\StringHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleExceptions;

class PointOfSaleImportable extends BaseService implements ImportableInterface
{
    private $allPointOfSales;
    private $repository;

    public function __construct(PointOfSaleRepository $repository)
    {
        $this->allPointOfSales = PointOfSale::all('cnpj');
        $this->repository      = $repository;
    }

    public function getColumns()
    {
        return [
            'network'      => trans('importables.network'),
            'slug'         => 'slug',
            'label'        => 'label',
            'tradingName'  => trans('importables.point_of_sale.tradingName'),
            'companyName'  => trans('importables.point_of_sale.companyName'),
            'areaCode'     => trans('importables.areaCode'),
            'cnpj'         => 'cnpj',
            'state'        => trans('importables.address.state'),
            'city'         => trans('importables.address.city'),
            'zipCode'      => trans('importables.address.zipCode'),
            'neighborhood' => trans('importables.address.neighborhood'),
            'local'        => trans('importables.address.local'),
            'number'       => trans('importables.address.number'),
            'claro'        => 'claro',
            'tim'          => 'tim',
            'oi'           => 'oi',
            'nextelCod'    => 'nextelCod',
            'nextelRef'    => 'nextelRef',
            'hierarchySlug' => trans('importables.hierarchy.slug'),
        ];
    }

    public function processLine($line): PointOfSale
    {
        $network     = $this->networkService->findOneBySlug($line['network']);
        $hierarchy   = $this->hierarchyService->findOneHierarchyBySlug($line['hierarchySlug']);
        $pointOfSale = $this->pointOfSaleService->findOneBySlugAndNetworkId($line['slug'], $network->id);

        $line['hierarchyId'] = $hierarchy->id;
        $line['networkId']   = $network->id;

        return $pointOfSale === null
            ? $this->create($network, $line)
            : $this->update($network, $pointOfSale, $line);
    }

    public function getType()
    {
        return Importables::POINTS_OF_SALE;
    }

    private function create(Network $network, array $line): PointOfSale
    {
        $this->cnpjExists($line);
        $line['providerIdentifiers'] = (new ValidateProviderToImport($network, $line))->make();

        return $this->repository->create($this->adapter($line));
    }

    private function update(Network $network, PointOfSale $pointOfSale, array $line): PointOfSale
    {
        $line['providerIdentifiers'] = (new ValidateProviderToImport($network, $line, $pointOfSale))->make();
        return $this->repository->update($pointOfSale, $this->adapter($line));
    }

    public function getExample(string $networkSlug = null, PointOfSale $pointOfSale = null, string $hierarchy = null)
    {
        return [
            $networkSlug ?? 'Rede Exemplo',
            $pointOfSale->slug ?? 'CGB123',
            $pointOfSale->label ?? 'Loja Exemplo',
            $pointOfSale->tradingName ?? 'Loja Exemplo Ltda',
            $pointOfSale->companyName ?? 'Rede Exemplo',
            $pointOfSale->areaCode ?? '11',
            $pointOfSale->cnpj ?? '36721217000138',
            $pointOfSale->state ?? 'SP',
            $pointOfSale->city ?? 'cidade',
            $pointOfSale->zipCode ?? '13123123',
            $pointOfSale->neighborhood ?? 'bairro teste',
            $pointOfSale->local ?? 'rua teste',
            $pointOfSale->number ?? '2',
            $pointOfSale->providerIdentifiers[Operations::CLARO] ?? '15F0',
            $pointOfSale->providerIdentifiers[Operations::TIM] ?? 'SP10_MGNORI_VA1880_AO11',
            $pointOfSale->providerIdentifiers[Operations::OI] ?? '1035863',
            $pointOfSale->providerIdentifiers[Operations::NEXTEL][NextelBRConstants::POINT_OF_SALE_COD] ?? '787489',
            $pointOfSale->providerIdentifiers[Operations::NEXTEL][NextelBRConstants::POINT_OF_SALE_REF] ?? '54154',
            $hierarchy ?? 'loja-regiao-sul'
        ];
    }

    private function adapter(array $line): array
    {
        return   [
            'networkId'    => $line['networkId'],
            'slug'         => $line['slug'],
            'label'        => StringHelper::removeSpecialcharactersAndAccent($line['label']) ?? '',
            'tradingName'  => StringHelper::removeSpecialcharactersAndAccent($line['tradingName']),
            'companyName'  => StringHelper::removeSpecialcharactersAndAccent($line['companyName']),
            'areaCode'     => $line['areaCode'],
            'cnpj'         => BrazilianDocuments::validateCnpj($line['cnpj']),
            'state'        => $line['state'],
            'city'         => $line['city'],
            'zipCode'      => BrazilianDocuments::unmask($line['zipCode']),
            'neighborhood' => $line['neighborhood'],
            'number'       => $line['number'],
            'providerIdentifiers' => $line['providerIdentifiers'],
            'hierarchyId'  => $line['hierarchyId']
        ];
    }

    private function cnpjExists(array $line)
    {
        $contains = $this->allPointOfSales->contains('cnpj', $line['cnpj']);

        if ($contains) {
            throw PointOfSaleExceptions::cnpjAlreadyExists();
        }

        return $line;
    }

    public static function buildExample()
    {
        $pointOfSaleImportable = resolve(__CLASS__);
        $columns               = array_values($pointOfSaleImportable->getColumns());
        $lines                 = $pointOfSaleImportable->getExample();

        return CsvHelper::arrayToCsv([$columns ,$lines]);
    }
}
