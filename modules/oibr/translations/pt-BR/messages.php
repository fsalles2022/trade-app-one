<?php

use OiBR\Enumerators\OiBRBusinessCodes;

return [
    'controle_cartao'                                       => [
        'eligibility' => [
            'available'   => 'Msisdn disponível para adesão.',
            'active'      => 'Linha já ativa no OI Controle.',
            'unavailable' => 'Msisdn na Blacklist ou em Pré-Cadastro.',
            'inactive'    => 'Linha inativa na operadora. Realize a reativação ligando para *144 do seu Oi / 1057 de qualquer telefone.',
        ],
        'success'     => 'Parabéns, ativação concluída',
        'failure'     => 'Não foi possível contratar o plano',
        'pending'     => 'Pré-Cadastro criado, aguardando notificação do Siebel para conclusão do cadastro',
        'migration_success' => 'Pedido realizado com sucesso! Em até 24h você receberá um SMS para confirmar a migração do seu Pré para Controle.'
    ],
    'controle_boleto'                                       => [
        'eligibility' => [
            'disponivel'   => 'Msisdn disponível para adesão.',
            'indisponivel' => 'Msisdn na Blacklist ou em Pré-Cadastro.',
            'ativo'        => 'Linha já ativa no OI Controle.',
            'inativo'      => 'Linha inativa na operadora. Realize a reativação ligando para *144 do seu Oi / 1057 de qualquer telefone.',
        ]
    ],
    OiBRBusinessCodes::MSISDN_BLOQUEADO                     => 'MSISDN_BLOQUEADO: Linha bloqueada para aquisição de um controle. Entre em contato com a Central de Atendimento da OI, ligando *144 do seu Oi / 1057 de qualquer telefone (fixo ou celular).',
    OiBRBusinessCodes::ASSINANTE_INVALIDO                   => 'ASSINANTE_INVALIDO: Linha antiga ou recentemente ativada, se faz necessário fazer uma ligação TARIFADA com o CHIP no aparelho. Após a ligação, refaça o processo de venda.',
    OiBRBusinessCodes::INTEGRACAO_INDISPONIVEL              => 'INTEGRACAO_INDISPONIVEL: Instabilidade momentânea, por favor tente novamente dentro de uns minutos!',
    OiBRBusinessCodes::VENDEDOR_INVALIDO                    => 'VENDEDOR_INVALIDO: Não é possível autenticar seu usuário. Entre em contato com seu superior para envio dos dados cadastrais para atualização.',
    OiBRBusinessCodes::VENDA_DDD_INVALIDA                   => 'VENDA_DDD_INVALIDA: Venda realizada para um DDD diferente da sua região. Sao permitidas vendas apenas para os DDDs da sua região. VERIFIQUE antes de prosseguir',
    OiBRBusinessCodes::PDV_INEXISTENTE                      => 'PDV_INEXISTENTE: Detectamos que seu Ponto De Venda não está cadastrado na operadora. Portano, seu PDV está impedido de finalizar a venda.',
    OiBRBusinessCodes::PDV_INATIVO                          => 'PDV_INATIVO: Detectamos que seu Ponto De Venda encontra-se inativo na operadora. Portano, seu PDV está impedido de finalizar a venda.',
    OiBRBusinessCodes::OFERTA_NAO_ENCONTRADA                => 'OFERTA_NAO_ENCONTRADA: Ocorreu uma instabilidade na operadora impossibilitando identificar a oferta escolhida. Por favor, realize um novo processo de venda ou ofereça outra oferta para o cliente.',
    OiBRBusinessCodes::OFERTA_INVALIDA                      => 'OFERTA_INVALIDA: Ocorreu uma instabilidade na operadora impossibilitando identificar a oferta escolhida. Por favor, realize um novo processo de venda ou ofereça outra oferta para o cliente.',
    OiBRBusinessCodes::MSISDN_NAO_DISPONIVEL                => 'MSISDN_NAO_DISPONIVEL: A linha não passou na análise da operadora, pois o número está indisponível. Para esclarecimentos ligue no *144 do seu Oi / 1057 de qualquer telefone (fixo ou celular).',
    OiBRBusinessCodes::MSISDN_INVALIDO                      => 'MSISDN_INVALIDO: Não foi possível autenticar o MSISDN como um número OI. Para esclarecimentos, fale com a operadora *144 do seu Oi / 1057 de qualquer telefone (fixo ou celular)',
    OiBRBusinessCodes::ESTABELECIMENTO_INVALIDO             => 'ESTABELECIMENTO_INVALIDO: O código do seu PDV está divergente na operadora. Informe seu superior sobre o erro, e oriente ele a entrar em contato a equipe TradeAppOne.',
    OiBRBusinessCodes::ERRO_HABILITAR_MONITORAMENTO_RECARGA => 'ERRO_HABILITAR_MONITORAMENTO_RECARGA: não foi possível reconhecer MSISDN como um número OI. Realize uma ligação TARIFADA para ativar o CHIP ou realize um novo processo de venda com outro MSISDN.',
    OiBRBusinessCodes::EMAIL_INVALIDO                       => 'EMAIL_INVALIDO: não foi possível validar o E-mail informado. Refaça o processo de venda informando outro E-mail.',
    OiBRBusinessCodes::DDD_INVALIDO                         => 'DDD_INVALIDO: não foi possível validar o DDD informado, por favor, refaça o processo de venda informando outro DDD',
    OiBRBusinessCodes::CPF_INVALIDO                         => 'CPF_INVALIDO: não foi possível validar o CPF informado, por favor, refaça a venda.',
    OiBRBusinessCodes::CONTATO_INVALIDO                     => 'CONTATO_INVALIDO: não foi possível validar o CONTATO informado, por favor, refaça o processo de venda informando outro contato.',
    OiBRBusinessCodes::CLIENTE_NAO_ENCONTRADO               => 'CLIENTE_NAO_ENCONTRADO: houve uma falha de comunicação na análise dos dados do cliente. Por favor, refaça a venda, caso o erro persista, sugira ao cliente mudar a forma de pagamente para um Controle Boleto.',
    OiBRBusinessCodes::CEP_INVALIDO                         => 'CEP_INVALIDO: não foi possível validar o CEP informado, por favor, refaça a venda.'

];
