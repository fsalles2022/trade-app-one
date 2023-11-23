<?php

namespace ClaroBR\Adapters;

use Carbon\Carbon;
use ClaroBR\Enumerators\SivOperations;
use ClaroBR\Enumerators\SivStatus;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ModesTranslation;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;

class MapSivSale
{

    const HOLDER             = 'HOLDER';
    const DEPENDENT          = 'DEPENDENT';
    const INTEGRATION_SYSTEM = 'SIV';

    public static function mapOne(PointOfSale $pointOfSaleEntity, ?User $userEntity, array $saleFromSiv)
    {
        $servicesFlat = [];
        $userCpf      = data_get($userEntity, 'user.cpf');

        $pointOfSale = [
            'pointofsale_id'              => data_get($pointOfSaleEntity, 'id'),
            'pointofsale_slug'            => data_get($pointOfSaleEntity, 'slug'),
            'pointofsale_label'           => data_get($pointOfSaleEntity, 'label'),
            'pointofsale_cnpj'            => data_get($pointOfSaleEntity, 'cnpj'),
            'pointofsale_network_slug'    => data_get($pointOfSaleEntity, 'network.slug'),
            'pointofsale_network_label'   => data_get($pointOfSaleEntity, 'network.label'),
            'pointofsale_hierarchy'       => data_get($pointOfSaleEntity, 'hierarchy.slug'),
            'pointofsale_hierarchy_label' => data_get($pointOfSaleEntity, 'hierarchy.label'),
            'pointofsale_state'           => data_get($pointOfSaleEntity, 'state'),
            'pointofsale_areaCode'        => data_get($pointOfSaleEntity, 'areaCode'),
        ];

        if ($userEntity) {
            $user = [
                'user_id'        => $userEntity->id,
                'user_cpf'       => $userEntity->cpf,
                'user_role'      => $userEntity->role->slug,
                'user_firstname' => $userEntity->firstName . ' ' . $userEntity->lastName,
            ];
        } else {
            $user = [
                'user_cpf'       => $userCpf,
                'user_firstname' => data_get($saleFromSiv, 'user.nome')
            ];
        }

        foreach ($saleFromSiv['services'] as $index => $serviceFromSiv) {
            $idVenda            = data_get($serviceFromSiv, 'venda_id');
            $saleTransaction    = ['saletransaction' => $idVenda];
            $serviceTransaction = self::generateIdentifier($saleFromSiv, $serviceFromSiv);

            $newSale = self::createNewSale($saleFromSiv, $serviceFromSiv, $serviceTransaction);
            $newSale = array_merge($saleTransaction, $pointOfSale, $user, $newSale);
            array_push($servicesFlat, $newSale);
            if (data_get($serviceFromSiv, 'dependents')) {
                foreach ($serviceFromSiv['dependents'] as $key => $dependentsInService) {
                    $serviceTransaction .= "_DEP_$key";
                    $newdependentSale    = self::createNewSale(
                        $saleFromSiv,
                        $dependentsInService,
                        $serviceTransaction,
                        self::DEPENDENT
                    );
                    $newdependentSale    = array_merge($saleTransaction, $pointOfSale, $user, $newdependentSale);
                    array_push($servicesFlat, $newdependentSale);
                }
            }
        }
        return $servicesFlat;
    }

    /**
     * @param array $saleFromSiv
     * @param $serviceFromSiv
     * @return string
     */
    private static function generateIdentifier(array $saleFromSiv, $serviceFromSiv): string
    {
        return data_get($saleFromSiv, 'id') . "-" . data_get($serviceFromSiv, 'id');
    }

    private static function createNewSale(
        array $saleFromSiv,
        array $serviceFromSiv,
        string $serviceTransaction,
        string $customerType = self::HOLDER
    ): array {
        $idVenda   = data_get($serviceFromSiv, 'venda_id');
        $customer  = data_get($saleFromSiv, 'customer');
        $createdAt = Carbon::parse(data_get($saleFromSiv, 'created_at'));
        $updatedAt = Carbon::parse(data_get($saleFromSiv, 'updated_at'));

        $operation   = self::getOperation($serviceFromSiv);
        $mode        = self::getMode($serviceFromSiv);
        $invoiceType = self::getInvoiceType($serviceFromSiv);
        $status      = data_get($serviceFromSiv, 'status');
        $statusTAO   = filled($status) ? data_get(SivStatus::ORIGINAL_STATUS, $status, '-') : '-';
        return array_filter([
            'source'                          => self::INTEGRATION_SYSTEM,
            'service_operator'                => Operations::CLARO,
            'service_sector'                  => Operations::TELECOMMUNICATION,
            'service_operation'               => $operation,
            'service_mode'                    => $mode,
            'service_product'                 => data_get($serviceFromSiv, 'plan.id'),
            'service_servicetransaction'      => $serviceTransaction,
            'service_label'                   => data_get($serviceFromSiv, 'plan.label'),
            'service_iccid'                   => data_get($serviceFromSiv, 'iccid'),
            'service_msisdn'                  => MsisdnHelper::removeCountryCode(
                MsisdnHelper::BR,
                data_get($serviceFromSiv, 'numero_acesso', '')
            ),
            'service_portednumber'            => MsisdnHelper::removeCountryCode(
                MsisdnHelper::BR,
                data_get($serviceFromSiv, 'portabilidade', '')
            ),
            'service_dueDate'                        => data_get($serviceFromSiv, 'vencimento'),
            'service_statusthirdparty'               => $status,
            'service_status'                         => $statusTAO,
            'service_invoiceType'                    => $invoiceType,
            'service_operator_pid'                   => $idVenda,
            'service_operator_sid'                   => data_get($serviceFromSiv, 'id'),
            'service_customer_cpf'                   => data_get($customer, 'cpf'),
            'service_price'                          => data_get($serviceFromSiv, 'valor'),
            'service_isPreSale'                      => data_get($serviceFromSiv, 'isPreSale'),
            'service_operatoridentifiers_acceptance' => data_get($serviceFromSiv, 'aceite_voz'),
            'service_customer_firstname'             => data_get($customer, 'nome'),
            'service_customer_lastname'              => data_get($customer, 'nome'),
            'service_customer_birthday'              => data_get($customer, 'data_nascimento'),
            'service_customer_gender'                => data_get($customer, 'genero'),
            'service_customer_filiation'             => data_get($customer, 'filiacao'),
            'service_customer_email'                 => data_get($customer, 'email'),
            'service_customer_mainphone'             => data_get($customer, 'telefone_principal'),
            'service_customer_secondaryphone'        => data_get($customer, 'telefone_secundario'),
            'service_customer_zipCode'               => data_get($customer, 'cep'),
            'service_customer_local'                 => data_get($customer, 'logradouro'),
            'service_customer_number'                => data_get($customer, 'numero'),
            'service_customer_neighborhood'          => data_get($customer, 'bairro'),
            'service_customer_state'                 => data_get($customer, 'uf'),
            'service_customer_city'                  => data_get($customer, 'cidade'),
            'service_promotion_label'                => data_get($serviceFromSiv, 'promotion.nome'),
            'service_promotion_price'                => data_get($serviceFromSiv, 'promotion.valor'),
            'customertype'                           => $customerType,
            'updatedAt'                              => $updatedAt,
            'createdAt'                              => $createdAt,
        ]);
    }

    private static function getOperation(array $ServiceFromSiv): string
    {
        $type = data_get($ServiceFromSiv, 'plano_tipo');

        switch ($type) {
            case SivOperations::POS_PAGO:
                $operation = Operations::CLARO_POS;
                break;
            case SivOperations::PRE_PAGO:
                $operation = Operations::CLARO_PRE;
                break;
            case SivOperations::CONTROLE_BOLETO:
                $operation = Operations::CLARO_CONTROLE_BOLETO;
                break;
            case SivOperations::CONTROLE_FACIL:
                $operation = Operations::CLARO_CONTROLE_FACIL;
                break;
            case SivOperations::BANDA_LARGA:
                $operation = Operations::CLARO_BANDA_LARGA;
                break;
            default:
                $operation = '';
                break;
        }
        return $operation;
    }

    public static function getMode(array $serviceFromSiv)
    {
        $serviceType = data_get($serviceFromSiv, 'tipo_servico');
        switch ($serviceType) {
            case ModesTranslation::ACTIVATION:
                if (filled(data_get($serviceFromSiv, 'portabilidade'))) {
                    $mode = Modes::PORTABILITY;
                } else {
                    $mode = Modes::ACTIVATION;
                }
                break;
            case ModesTranslation::MIGRATION:
                $mode = Modes::MIGRATION;
                break;
            default:
                $mode = '';
                break;
        }
        return $mode;
    }

    public static function getInvoiceType(array $serviceFromSiv)
    {
        $controle    = ['EMAIL' => 'EMAIL', 'VIA_POSTAL' => 'VIA_POSTAL', 'DEBITO_AUTOMATICO' => 'DEBITO_AUTOMATICO'];
        $invoiceType = data_get($serviceFromSiv, 'tipo_fatura');
        return data_get($controle, $invoiceType ?? '');
    }
}
