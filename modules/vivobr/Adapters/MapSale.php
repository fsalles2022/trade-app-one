<?php

namespace VivoBR\Adapters;

use Carbon\Carbon;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use VivoBR\Enumerators\SunStatus;
use VivoBR\Models\VivoControle;

class MapSale
{
    const TYPE_POS      = 'PóS';
    const TYPE_PRE      = 'PRé';
    const TYPE_CONTROLE = 'CONTROLE';

    public static function mapOne(?PointOfSale $pointOfSaleEntity, ?User $userEntity, $source, array $saleFromSun)
    {
        $servicesFlat    = [];
        $customer        = data_get($saleFromSun, 'pessoa');
        $idVenda         = data_get($saleFromSun, 'id');
        $pointOfSaleCnpj = data_get($saleFromSun, 'cnpjPdv');
        $userCpf         = data_get($saleFromSun, 'cpfVendedor');
        $saleTransaction = ['saletransaction' => $idVenda];
        $createdAt       = Carbon::parse(data_get($saleFromSun, 'data'));
        $updatedAt       = Carbon::parse(data_get($saleFromSun, 'dataAlteracao'));
        if ($pointOfSaleEntity) {
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
        } else {
            $pointOfSale = [
                'pointofsale_cnpj'          => $pointOfSaleCnpj,
                'pointofsale_network_slug'  => data_get($saleFromSun, 'rede'),
                'pointofsale_network_label' => data_get($saleFromSun, 'redeNomeFantasia'),
                'pointofsale_state'         => data_get($saleFromSun, 'uf')
            ];
        }

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
                'user_firstname' => data_get($saleFromSun, 'nomeCadastro')
            ];
        }

        foreach ($saleFromSun['servicos'] as $index => $serviceFromSun) {
            $status      = data_get($serviceFromSun, 'status');
            $operation   = self::getOperation($serviceFromSun);
            $mode        = self::getMode($serviceFromSun);
            $invoiceType = self::getInvoiceType($serviceFromSun);
            $statusTAO   = filled($status) ? data_get(SunStatus::ORIGINAL_STATUS, $status, '-') : '-';
            $newSale     = array_filter([
                'source'                          => $source,
                'service_operator'                => Operations::VIVO,
                'service_sector'                  => Operations::TELECOMMUNICATION,
                'service_operation'               => $operation,
                'service_mode'                    => $mode,
                'service_product'                 => data_get($serviceFromSun, 'idPlano'),
                'service_servicetransaction'      => self::generateIdentifier($saleFromSun, $serviceFromSun),
                'service_label'                   => data_get($serviceFromSun, 'nomePlano'),
                'service_iccid'                   => data_get($serviceFromSun, 'iccid'),
                'service_msisdn'                  => data_get($serviceFromSun, 'numeroAcesso'),
                'service_portednumber'            => data_get($serviceFromSun, 'numeroPortabilidade'),
                'service_dueDate'                 => data_get($serviceFromSun, 'vencimento'),
                'service_statusthirdparty'        => $status,
                'service_status'                  => $statusTAO,
                'service_invoiceType'             => $invoiceType,
                'service_operator_pid'            => $idVenda,
                'service_operator_sid'            => data_get($serviceFromSun, 'id'),
                'service_customer_cpf'            => data_get($customer, 'cpf'),
                'service_customer_firstname'      => data_get($customer, 'nome'),
                'service_customer_lastname'       => data_get($customer, 'nome'),
                'service_customer_birthday'       => self::validateBirthday(data_get($customer, 'dataNascimento')),
                'service_customer_gender'         => data_get($customer, 'sexo'),
                'service_customer_filiation'      => data_get($customer, 'filiacao'),
                'service_customer_email'          => data_get($customer, 'email'),
                'service_customer_mainphone'      => data_get($customer, 'telefone1'),
                'service_customer_secondaryphone' => data_get($customer, 'telefone2'),
                'service_customer_zipCode'        => data_get($customer, 'cep'),
                'service_customer_local'          => data_get($customer, 'logradouro'),
                'service_customer_number'         => data_get($customer, 'numero'),
                'service_customer_neighborhood'   => data_get($customer, 'bairro'),
                'service_customer_state'          => data_get($customer, 'UF'),
                'service_customer_city'           => data_get($customer, 'cidade'),
                'updatedAt'                       => $updatedAt,
                'createdAt'                       => $createdAt,
            ]);

            $newSale = array_merge($saleTransaction, $pointOfSale, $user, $newSale);
            array_push($servicesFlat, $newSale);
        }
        return $servicesFlat;
    }

    private static function getOperation(array $serviceFromSun): string
    {
        $type        = data_get($serviceFromSun, 'produto');
        $invoiceType = self::getInvoiceType($serviceFromSun);
        switch ($type) {
            case self::TYPE_POS:
                $operation = Operations::VIVO_POS_PAGO;
                break;
            case self::TYPE_PRE:
                $operation = Operations::VIVO_PRE;
                break;
            case self::TYPE_CONTROLE:
                if (array_search($invoiceType, VivoControle::INVOICE_TYPES)) {
                    $operation = Operations::VIVO_CONTROLE;
                } else {
                    $operation = Operations::VIVO_CONTROLE_CARTAO;
                }
                break;
            default:
                $operation = '';
                break;
        }
        return $operation;
    }

    public static function getInvoiceType(array $serviceFromSun)
    {
        $controle    = ['E-mail' => 'EMAIL', 'Via Postal' => 'VIA_POSTAL'];
        $invoiceType = data_get($serviceFromSun, 'tipoFatura');
        return data_get($controle, $invoiceType);
    }

    public static function getMode(array $serviceFromSun)
    {
        $serviceType = data_get($serviceFromSun, 'tipoServico');
        switch ($serviceType) {
            case 'ALTA':
                if (filled(data_get($serviceFromSun, 'numeroPortabilidade'))) {
                    $mode = Modes::PORTABILITY;
                } else {
                    $mode = Modes::ACTIVATION;
                }
                break;
            case 'MIGRACAO':
                $mode = Modes::MIGRATION;
                break;
            default:
                $mode = '';
                break;
        }
        return $mode;
    }

    /**
     * @param array $saleFromSun
     * @param $serviceFromSun
     * @return string
     */
    private static function generateIdentifier(array $saleFromSun, $serviceFromSun): string
    {
        return data_get($saleFromSun, 'id') . "-" . data_get($serviceFromSun, 'id');
    }

    private static function validateBirthday($date)
    {
        if ($date == '0000-00-00') {
            return null;
        }

        return $date;
    }
}
