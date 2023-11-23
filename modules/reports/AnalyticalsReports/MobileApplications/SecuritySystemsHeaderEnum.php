<?php

namespace Reports\AnalyticalsReports\MobileApplications;

final class SecuritySystemsHeaderEnum
{
    public const CHANNEL = 'Canal';
    public const SOURCE  = 'Origem';

    public const SERVICE_SECTOR     = 'Serviço';
    public const SERVICE_OPERATOR   = 'Operadora';
    public const SERVICE_SERVICE_ID = 'Serviço ID';
    public const SERVICE_MODE       = 'Tipo Serviço';
    public const SERVICE_LABEL      = 'Plano';
    public const SERVICE_OPERATION  = 'Plano Tipo';

    public const POINTOFSALE_HIERARCHY_LABEL = 'Regional';
    public const CREATED_AT_DATE             = 'Data Venda';
    public const CREATED_AT_HOUR             = 'Hora Venda';

    public const SERVICE_STATUS     = 'Status Venda';
    public const SERVICE_PRICE      = 'Valor Servico';
    public const PAYMENT_TIMES      = 'Qnt Parcelas';
    public const SERVICE_IMEI       = 'IMEI';
    public const SERVICE_LOG_STATUS = 'Status log';
    public const TRANSACTION_ID     = 'ID da transação';
    public const LOG_PAYMENT_STATUS = 'Log do Status Pagamento';

    public const SERVICE_DEVICE_MODEL        = 'Modelo Aparelho';
    public const SERVICE_DEVICE_PRICEWITH    = 'Preço Aparelho';
    public const HAS_RECOMMENDATION          = 'Possui Indicação';
    public const RECOMMENDATION_REGISTRATION = 'Indicação da Venda';

    public const USER_FULL_NAME = 'Nome Vendedor';
    public const USER_CPF       = 'Cpf Vendedor';

    public const SERVICE_CUSTOMER_FULL_NAME = 'Nome Cliente';
    public const SERVICE_CUSTOMER_CPF       = 'CPF Cliente';
    public const SERVICE_CUSTOMER_CITY      = 'Cidade Cliente';
    public const SERVICE_CUSTOMER_STATE     = 'UF Cliente';
    public const SERVICE_CUSTOMER_EMAIL     = 'Email Cliente';
    public const CUSTOMERTYPE               = 'Tipo Cliente';

    public const POINTOFSALE_LABEL         = 'Nome Pdv';
    public const POINTOFSALE_CNPJ          = 'CNPJ Pdv';
    public const POINTOFSALE_CITY          = 'Cidade Pdv';
    public const POINTOFSALE_STATE         = 'UF Pdv';
    public const POINTOFSALE_NETWORK_LABEL = 'Nome Rede';
    public const POINTOFSALE_NETWORK_SLUG  = 'Codigo da Rede';
    public const POINTOFSALE_SLUG          = 'Codigo do Pdv';
    public const POINTOFSALE_CEP           = 'Cep do Pdv';
}
