<?php

namespace Reports\AnalyticalsReports\MobileApplications;

final class MobileApplicationsHeaderEnum
{
    const CHANNEL = 'Canal';
    const SOURCE  = 'Origem';

    const SERVICE_SECTOR     = 'Serviço';
    const SERVICE_OPERATOR   = 'Operadora';
    const SERVICE_SERVICE_ID = 'Serviço ID';
    const SERVICE_MODE       = 'Tipo Serviço';
    const SERVICE_LABEL      = 'Plano';
    const SERVICE_OPERATION  = 'Plano Tipo';

    const POINTOFSALE_HIERARCHY_LABEL = 'Regional';
    const CREATED_AT_DATE             = 'Data Venda';
    const CREATED_AT_HOUR             = 'Hora Venda';

    const SERVICE_STATUS = 'Status Venda';
    const SERVICE_PRICE  = 'Valor Servico';
    const SERVICE_IMEI   = 'IMEI';

    const SERVICE_DEVICE_MODEL     = 'Modelo Aparelho';
    const SERVICE_DEVICE_PRICEWITH = 'Preço Aparelho';

    const USER_FULL_NAME = 'Nome Vendedor';
    const USER_CPF       = 'Cpf Vendedor';

    const SERVICE_CUSTOMER_FULL_NAME = 'Nome Cliente';
    const SERVICE_CUSTOMER_CPF       = 'CPF Cliente';
    const SERVICE_CUSTOMER_CITY      = 'Cidade Cliente';
    const SERVICE_CUSTOMER_STATE     = 'UF Cliente';
    const CUSTOMERTYPE               = 'Tipo Cliente';

    const POINTOFSALE_LABEL         = 'Nome Pdv';
    const POINTOFSALE_CNPJ          = 'CNPJ Pdv';
    const POINTOFSALE_CITY          = 'Cidade Pdv';
    const POINTOFSALE_STATE         = 'UF Pdv';
    const POINTOFSALE_NETWORK_LABEL = 'Nome Rede';
    const POINTOFSALE_NETWORK_SLUG  = 'Codigo da Rede';
    const POINTOFSALE_SLUG          = 'Codigo do Pdv';
}
