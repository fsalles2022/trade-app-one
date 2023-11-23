<?php

namespace TradeAppOne\Domain\Exportables;

final class AnalyticalReportHeaderEnum
{
    public const CHANNEL = 'Canal';
    public const SOURCE  = 'Origem';

    public const SERVICE_SECTOR      = 'Serviço';
    public const SERVICE_OPERATOR    = 'Operadora';
    public const SERVICE_SERVICE_ID  = 'Serviço ID';
    public const SERVICE_OPERATOR_ID = 'Operadora ID';
    public const SERVICE_AREACODE    = 'DDD';
    public const SERVICE_MODE        = 'Tipo Serviço';
    public const SERVICE_LABEL       = 'Plano';
    public const SERVICE_RECURRENCE  = 'Recorrência';
    public const SERVICE_OPERATION   = 'Plano Tipo';

    public const POINTOFSALE_HIERARCHY_LABEL = 'Regional';
    public const CREATED_AT_DATE             = 'Data Venda';
    public const CREATED_AT_HOUR             = 'Hora Venda';
    public const UPDATED                     = 'Atualizado';

    public const SERVICE_INVOICETYPE     = 'Tipo Fatura';
    public const DUE_DAY                 = 'Vencimento';
    public const SERVICE_STATUS          = 'Status Venda';
    public const SERVICE_PRICE           = 'Valor Servico';
    public const SERVICE_DONATE_CHIP     = 'Doação de Chip';
    public const SERVICE_DISCOUNT_VALUE  = 'Valor Desconto';
    public const SERVICE_RECHARGE        = 'Valor Recarga';
    public const SERVICE_MSISDN          = 'MSISDN';
    public const SERVICE_PORTEDNUMBER    = 'MSISDN Portado';
    public const SERVICE_ICCID           = 'ICCID';
    public const SERVICE_IMEI            = 'IMEI';
    public const SERVICE_ACCEPTANCE      = 'Aceite';
    public const SERVICE_OPERATOR_STATUS = 'Status Operadora';

    public const SERVICE_DEVICE_SKU          = 'SKU Aparelho';
    public const SERVICE_DEVICE_MODEL        = 'Modelo Aparelho';
    public const SERVICE_DEVICE_PRICEWITH    = 'Preço Aparelho';
    public const SERVICE_DEVICE_PRICEWITHOUT = 'Aparelho sem desconto';
    public const SERVICE_DEVICE_DISCOUNT     = 'Aparelho desconto';

    public const USER_FULL_NAME              = 'Nome Vendedor';
    public const USER_CPF                    = 'Cpf Vendedor';
    public const ENROLLMENT                  = 'Matricula Vendedor';
    public const USER_ASSOCIATIVE            = 'Promotor Associado';
    public const HAS_RECOMMENDATION          = 'Possui Indicação';
    public const RECOMMENDATION_REGISTRATION = 'Indicação da Venda';

    public const SERVICE_CUSTOMER_FULL_NAME        = 'Nome Cliente';
    public const SERVICE_CUSTOMER_BIRTH            = 'Nascimento Cliente';
    public const SERVICE_CUSTOMER_CPF              = 'CPF Cliente';
    public const SERVICE_CUSTOMER_RG               = 'RG Cliente';
    public const SERVICE_CUSTOMER_RG_DATE          = 'RG Data de emissão Cliente';
    public const SERVICE_CUSTOMER_RG_LOCAL         = 'RG Orgao emissor Cliente';
    public const SERVICE_CUSTOMER_RG_STATE         = 'RG UF Emissor Cliente';
    public const SERVICE_CUSTOMER_CITY             = 'Cidade Cliente';
    public const SERVICE_CUSTOMER_LOCAL            = 'Endereco Cliente';
    public const SERVICE_CUSTOMER_LOCAL_NUMBER     = 'Numero Endereco Cliente';
    public const SERVICE_CUSTOMER_STATE            = 'UF Cliente';
    public const SERVICE_CUSTOMER_ZIPCODE          = 'CEP Cliente';
    public const SERVICE_CUSTOMER_TYPE_OF_ADDRESS  = 'Tipo de Logradouro';
    public const SERVICE_CUSTOMER_COMPLEMENT       = 'Complemento';
    public const SERVICE_CUSTOMER_EMAIL            = 'E-mail';
    public const SERVICE_CUSTOMER_MAIN_PHONE       = 'Telefone p/ contato 1';
    public const SERVICE_CUSTOMER_SECONDARY_PHONE  = 'Telefone p/ contato 2';
    public const SERVICE_CUSTOMER_FILIATION        = 'Filiação';
    public const CUSTOMER_TYPE                     = 'Tipo Cliente';

    public const SERVICE_WITNESS_NAME_1 = 'Nome testemunha 1';
    public const SERVICE_WITNESS_RG_1   = 'RG Testemunha 1';
    public const SERVICE_WITNESS_NAME_2 = 'Nome testemunha 2';
    public const SERVICE_WITNESS_RG_2   = 'RG Testemunha 2';

    public const POINTOFSALE_LABEL         = 'Nome Pdv';
    public const POINTOFSALE_CNPJ          = 'CNPJ Pdv';
    public const POINTOFSALE_CITY          = 'Cidade Pdv';
    public const POINTOFSALE_STATE         = 'UF Pdv';
    public const POINTOFSALE_NETWORK_LABEL = 'Nome Rede';
    public const POINTOFSALE_NETWORK_SLUG  = 'Codigo da Rede';
    public const POINTOFSALE_SLUG          = 'Codigo do Pdv';

    public const RECOMMENDATION_NAME = 'Nome Indicação';
    public const BKO_OBSERVATION     = 'Observações';
}
