<?php

namespace Integrators\Adapters;

use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;

class ResidentialSaleAdapter
{
    public static function adapt($saleData): array
    {
        $saleData = collect($saleData);

        $pointOfSale = self::getPointOfSaleFromResidential(data_get($saleData, 'pos.codigo', ''));
        $user        = self::getUserFromResidential(data_get($saleData, 'user.cpf'));

        return array_filter([
            'user' => $user,
            'pointOfSale' => $pointOfSale,
            'channel' => Channels::VAREJO,
            'source' => data_get($saleData, 'source'),
            'services' => self::adaptServices(data_get($saleData, 'services'), data_get($saleData, 'id')),
            'total' => data_get($saleData, 'total'),
            Sale::CREATED_AT => data_get($saleData, 'created_at'),
        ]);
    }

    private static function getPointOfSaleFromResidential(string $pointOfSaleCode): ?PointOfSale
    {
        $pointOfSaleRepository = resolve(PointOfSaleRepository::class);
        return $pointOfSaleRepository->findOneByProviderIdentifiers(Operations::CLARO, $pointOfSaleCode);
    }

    private static function adaptServices($services, $saleId): array
    {
        $adaptedServices = [];
        foreach ($services as $service) {
            $service = collect($service);
            preg_match('/([A-Z].+\s)([A-Z].+)$/m', data_get($service, 'customer.nome'), $customerName);
            $adaptedService = array_filter([
                'product' => data_get($service, 'plano_id'),
                'operator' => data_get($service, 'operadora'),
                'operation' => data_get($service, 'plano_tipo'),
                'label' => data_get($service, 'plano'),
                'mode' => ResidentialConstantsMap::SIV_MODES[data_get($service, 'tipo_servico', Modes::ACTIVATION)],
                'iccid' => data_get($service, 'iccid'),
                'dueDate' => data_get($service, 'vencimento'),
                'invoiceType' => data_get($service, 'tipo_fatura'),
                'bankId' => data_get($service, 'banco_id'),
                'agency' => data_get($service, 'agencia'),
                'checkingAccount' => data_get($service, 'conta_corrente'),
                'portedNumber' => data_get($service, 'portabilidade'),
                'msisdn' => data_get($service, 'numero_acesso'),
                'portedLineOperator' => data_get($service, 'operadora_portabilidade'),
                'customer' => array_filter([
                    'cpf' => data_get($service, 'customer.cpf'),
                    'firstName' => $customerName[1],
                    'lastName' => $customerName[2],
                    'birthday' => data_get($service, 'customer.data_nascimento'),
                    'email' => data_get($service, 'customer.email'),
                    'mainPhone' => data_get($service, 'customer.telefone_principal'),
                    'secondaryPhone' => data_get($service, 'customer.telefone_secundario'),
                    'gender' => data_get($service, 'customer.genero'),
                    'filiation' => data_get($service, 'customer.filiacao'),
                    'zipCode' => data_get($service, 'customer.cep'),
                    'state' => data_get($service, 'customer.uf'),
                    'city' => data_get($service, 'customer.cidade'),
                    'neighborhood' => data_get($service, 'customer.bairro'),
                    'local' => data_get($service, 'customer.logradouro'),
                    'number' => data_get($service, 'customer.numero'),
                    'complement' => data_get($service, 'customer.complemento'),
                ]),
                'sector' => data_get($service, 'setor'),
                'status' => ResidentialConstantsMap::RESIDENTIAL_STATUS[data_get($service, 'plano_tipo')][data_get($service, 'status')],
                'statusThirdParty' => data_get($service, 'status'),
                'operatorIdentifiers' => [
                    'venda_id' => $saleId,
                    'servico_id' => data_get($service, 'id')
                ],
                'price' => data_get($service, 'valor'),
                'log' => data_get($service, 'log')
            ]);

            $adaptedService['contractNumber']   = (string) data_get($service, 'numero_contrato', '');
            $adaptedService['ibgeCode']         = (string) data_get($service, 'codigo_ibge', '');
            $adaptedService['installationDate'] = (string) data_get($service, 'data_instalacao', '');

            $adaptedServices[] = $adaptedService;
        }
        return $adaptedServices;
    }

    private static function getUserFromResidential(?string $cpfUser): ?array
    {
        $userRepository = resolve(UserRepository::class);
        $user           = $userRepository->findOneBy('cpf', $cpfUser);
        $user           = collect([$user])->transform(static function ($userItem) {
            return [
                'id' => data_get($userItem, 'id'),
                'role' => data_get($userItem, 'role.slug'),
                'firstName' => data_get($userItem, 'firstName'),
                'lastName' => data_get($userItem, 'lastName'),
                'cpf' => data_get($userItem, 'cpf'),
                'email' => data_get($userItem, 'email'),
            ];
        });
        return $user->first();
    }
}
