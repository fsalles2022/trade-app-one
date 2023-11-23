<?php

namespace McAfee\Enumerators;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;

class McAfeePlans
{
    public static function get(): Collection
    {
        $additionalDetail = array_merge(
            self::defaultDetail(),
            ['Pagamentos com Cartão: Existem alguns bancos emissores que não aceitam parcelas menores que R$ 10,00.']
        );

        return collect([
            [
                "id"                => "1341",
                "label"             => "McAfee Mobile Security 1 Dispositivo",
                "product"           => McAfeeSKU::MOBILE_SECURITY,
                "operation"         => Operations::MCAFEE_MOBILE_SECURITY,
                "price"             => "118.8",
                "installmentNumber" => "12",
                "quantity"          => "1",
                'details'           => $additionalDetail
            ],
            [
                "id"                => "1342",
                "label"             => "McAfee Multi Access 1 Dispositivo",
                "product"           => McAfeeSKU::MMA_YEARLY_1_DEVICE,
                "operation"         => Operations::MCAFEE_MULTI_ACCESS,
                "price"             => "59.88",
                "installmentNumber" => "12",
                "quantity"          => "1",
                'details'           => $additionalDetail
            ],
            [
                "id"                => "1343",
                "label"             => "McAfee Multi Access 3 Dispositivos",
                "product"           => McAfeeSKU::MMA_YEARLY_3_DEVICE,
                "operation"         => Operations::MCAFEE_MULTI_ACCESS,
                "price"             => "130.80",
                "installmentNumber" => "12",
                "quantity"          => "3",
                'details'           => self::defaultDetail()
            ],
            [
                "id"                => "1344",
                "label"             => "McAfee Multi Access 5 Dispositivos",
                "product"           => McAfeeSKU::MMA_YEARLY_5_DEVICE,
                "operation"         => Operations::MCAFEE_MULTI_ACCESS,
                "price"             => "178.80",
                "installmentNumber" => "12",
                "quantity"          => "5",
                'details'           => self::defaultDetail()
            ]
        ]);
    }
    public static function available(): array
    {
        return self::get()->pluck('id')->toArray();
    }

    private static function defaultDetail(): array
    {
        return [
            'Benefícios do McAfee Multi Access',
            'As prioridades dos consumidores mudam com o tempo. O mais importante agora é proteção da privacidade e da identidade.',
            'Defenda seu cliente contra as ameaças online mais recentes.',
            '1 ano de proteção online para smartphones, tablets ou notebooks.',
            'Seu cliente pode pagar em até 12 x sem juros no cartão de crédito e ainda pode ganhar 30 dias grátis/trial**, basta que você clique na opção SIM na tela logo a seguir.',
            '1) <strong>Proteja a privacidade do cliente </strong> e disfarça a identidade on-line de olhares curiosos, utilizando a tecnologia de criptografia Wi-Fi de nível bancário para que os consumidores possam se conectar com confiança e segurança. ',
            '2) A VPN da McAfee máscara o endereço IP do usuário. <br> Com a tecnologia de criptografia Wi-Fi de nível bancário, para que seus dados pessoais e atividades on-line sejam mantidos privados de hackers mesmo quando conectados a um Wi-Fi público ou a uma rede não segura.',
            '3) <strong>O McAfee WebAdvisor</strong> é seu companheiro confiável que ajuda a proteger você contra ameaças enquanto navega e pesquisa na Web.',
            '4) <strong>Navegação Segura</strong> - Nem todos os sites são o que parecem.O McAfee irá bloquear automaticamente sites maliciosos para seu cliente.',
            '5) <strong>Proteção da identidade </strong> - Vamos monitorar os feeds da dark web em busca de informações roubadas relacionadas ao e-mail do seu cliente e outras informações pessoais e proporcionaremos a ele as ferramentas de recuperação.',
            '6) <strong>Gerenciador de Senhas </strong> - A partir do momento que você abre o navegador, a extensão do True Key ajuda a acessar suas contas com facilidade.',
            '• Armazene e preencha de forma automática os detalhes das suas senhas.',
            '• Aproveite o acesso prático aos seus aplicativos, sites e dispositivos',
            '7) Compatível com todos os sistemas operacionais Windows® | macOS® | Android™ | iOS®',
            '8) Segurança premiada para a Internet. Desempenho confiável.',
            '<strong>Importante:</strong>',
            'Após o término do período do trial, caso o cliente não solicite o cancelamento, a cobrança será feita diretamente no cartão de crédito informado no ato da venda.',
            '<strong>**</strong> Caso a venda seja cancelada, não haverá pagamento de comissão para a rede/vendedor(a).',
            'Os cancelamentos solicitados pelo consumidor devem ser feitos através do chat disponível no site mcafeeexpress.com.br ou através do telefone (11) 3003-0930.',
            'Já cancelamentos solicitados pela loja devem ser feitos diretamente pelo chat disponível na ferramenta de vendas TradeAppOne.'
        ];
    }
}
