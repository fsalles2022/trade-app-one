<?php

use TradeAppOne\Domain\Enumerators\Permissions;

return [
    Permissions::SALE_CREATE => 'Registrar venda',
    Permissions::SALE_FLOW => 'Ver o fluxo de venda',
    Permissions::DASHBOARD_VIEW => 'Ver o dashboard',

    Permissions\SalePermission::NAME => [
        Permissions\SalePermission::ACTIVATE => "Ativar uma venda",
        Permissions\SalePermission::CREATE => "Criar venda",
        Permissions\SalePermission::EDIT => "Criar venda",
        Permissions\SalePermission::FLOW => "Ver fluxo de venda",
        Permissions\SalePermission::LOG => "Ver informações do log de uma venda",
        Permissions\SalePermission::PRINT => "Imprimir venda",
        Permissions\SalePermission::VIEW => "Ver listagem de vendas",
        Permissions\SalePermission::VIEW_ONLY_TRADE_IN => "Ver listagem de vendas do buyback apenas",
    ],

    Permissions\AnalyticalReportPermission::NAME => [
        Permissions\AnalyticalReportPermission::EXPORT => "Exportar o relátorio analítico",
        Permissions\AnalyticalReportPermission::VIEW => "Visualizar o relátorio analítico",
    ],

    Permissions\ManagementReportPermission::NAME => [
        Permissions\ManagementReportPermission::VIEW => "Visualizar o relatório gerencial",
    ],

    Permissions\GoalPermission::NAME => [
        Permissions\GoalPermission::IMPORT => "Importar as metas",
    ],

    Permissions\BannerPermission::NAME => [
        Permissions\BannerPermission::CREATE => "Criar banner",
        Permissions\BannerPermission::EDIT => "Editar banner",
        Permissions\BannerPermission::VIEW => "Visualizar os banners"
    ],

    Permissions\ManualPermission::NAME => [
        Permissions\ManualPermission::LINE_ACTIVATION => "Ver os manuais das linhas de operadoras",
        Permissions\ManualPermission::FAQ => "Ver os manuais de perguntas frequentes",
        Permissions\ManualPermission::PLANS_PORTOFOLIO => "Ver os portfólios para as linhas de operadoras",
        Permissions\ManualPermission::TRADE_IN => "Ver os Manuais para processo de Trade In"
    ],

    Permissions\NetworkPermission::NAME => [
        Permissions\NetworkPermission::UPDATE_PREFERENCES => "Atualizar preferências de configuração da rede",
        Permissions\NetworkPermission::CREATE => "Criar Rede"
    ],

    Permissions\DashboardPermission::NAME => [
        Permissions\DashboardPermission::VIEW => "Visualizar o painel e controle",
    ],

    Permissions\ImportablePermission::NAME => [
        Permissions\ImportablePermission::USER => "Importar usuários",
        Permissions\ImportablePermission::POINT_OF_SALE => "Importar pontos de venda",
        Permissions\ImportablePermission::OI_RESIDENTIAL_SALE => "Importar vendas residenciais da Oi",
        Permissions\ImportablePermission::AUTOMATIC_REGISTRATION => "Importar cadastro único de usuários",

    ],

    Permissions\PointOfSalePermission::NAME => [
        Permissions\PointOfSalePermission::VIEW=> "Ver listagem de pontos de venda",
    ],

    Permissions\UserPermission::NAME => [
        Permissions\UserPermission::EXPORT=> "Exportar Usuários",
        Permissions\UserPermission::PERSONIFY=> "Assumir temporariamente identidade de outro usuário",
        Permissions\UserPermission::VIEW=> "Ver listagem de usuários",
        Permissions\UserPermission::CREATE=> "Cadastrar ou atualizar novos usuários",
    ],

    Permissions\RecoveryPermission::NAME => [
        Permissions\RecoveryPermission::APPROVE=> "Aprovar um pedido de reset de senha",
        Permissions\RecoveryPermission::VIEW=> "Ver listagem de pedidos de reset de senha",
        Permissions\RecoveryPermission::REJECT=> "Rejeitar um pedido de reset de senha",
    ]
];
