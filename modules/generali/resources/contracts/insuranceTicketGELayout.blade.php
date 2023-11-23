<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <style type="text/css">
        @page {
            margin: 80px 25px 90px 25px;
        }

        @font-face {
            font-family: Helvetica, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
        }

        header {
            position: fixed;
            top: -50px;
            left: 25px;
            right: 0;
            /*height: 50px;*/
        }

        header > table, footer > table {
            width: 100%;
        }

        header img {
            height: 60px;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 25px;
            right: 0;
            /*height: 50px;*/
        }

        body {
            font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif, serif;
            font-size: 10.6pt;
            line-height: normal;
            box-sizing: border-box;
            text-align: center;
        }

        .voucher {
            margin-top: 25px;
            margin-left: 50px;
            margin-right: 50px;
            /*border: 1px solid black;*/
        }

        .table-area {
            width: 100%;
            padding: 0;
            border-spacing: 0;
            margin: 3px 0;
        }

        .table-area > thead > tr > th {
            text-transform: uppercase;
            background-color: #c00000;
            border-top: 1px solid #777;
            color: #d9d9d9;
            padding: 3px 3px 3px 5px;
        }

        .table-area > tbody > tr > td {
            /*background-color: #6b9dbb;*/
            border-left: 1px solid #999;
            border-bottom: 1px solid #999;
            font-size: 1rem;
        }

        .table-area > tbody > tr > td:first-child {
            border-left: 0;
        }

        .table-area > tbody > tr > td:last-child {
            border-right: 0;
        }

        .table-area > tbody > tr td > p .title {
            font-size: 1rem;
            color: #777;
            padding: 3px 5px;
            margin: 0;
        }

        .table-area > tbody > tr td > .title {
            font-size: 1rem;
            color: #777;
            padding: 3px 5px;
            margin: 0;
        }

        .table-area > tbody > tr td > .content {
            padding: 4px 0 4px 3px;
            color: #444;
        }

        .table-area > tbody > tr td > p.content {
            padding: 0 0 0 3px;
            margin: 3px 0;
            line-height: 12px;
        }

        .table-area > tbody > tr td > ol {
            padding: 5px 0 0 17px;
            margin: 2px 0;
            line-height: 12px;
            color: #444;
        }

        .text-center {
            text-align: center;
        }

        span.bold {
            font-weight: bold;
        }

        .table-info {
            width: 85%;
            border-collapse: collapse;
            border: 1px solid #888;
            margin: 10px auto;
            padding: 3px;
            text-align: center;
        }

        .table-info > thead > tr > th, .table-info > tbody > tr > td {
            border: 1px solid #888;
            padding: 5px;
            text-align: center;
        }

        .signatures {
            width: 100%;
        }

        .signatures .signature {
            width: 40%;
            text-align: center;
            border-top: 1px solid black;
            padding-top: 3px;
            vertical-align: initial;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
</head>
<body lang=PT-BR>
<header>
    <table>
        <tr>
            <td><img alt="Logo Generali" src="{{$layout->getImage('logo-generali')}}"></td>
            <td><img alt="Barras" style="float:right;" src="{{$layout->getImage('bars')}}"></td>
        </tr>
    </table>
</header>
<footer>
    <table>
        <tr>
            <td><img alt="Logo Generali" style="height: 40px;" src="{{$layout->getImage('logo-generali2')}}"></td>
            <td>
                <p style="text-align: right;">
                    <span style="font-weight: bold">GENERALI BRASIL SEGUROS S/A</span>
                    <br>
                    (CNPJ: 33.072.307/0001-7 • CÓD. SUSEP: 0590 8)
                </p>
            </td>
        </tr>
    </table>
</footer>

<div class="voucher">
    <div>
        <p style="font-weight: bold">
            <span style="color: #c00000; font-size: 1.9rem;">BILHETE DE SEGURO GENERALI GARANTIA ESTENDIDA ORIGINAL</span><br>
            <span style="color: #a6a6a6; font-size: 1.5rem">Ramo de Seguro: Extensão de garantia Original (0195) - Processo SUSEP: 15414.902107/2019-18</span>
        </p>
    </div>
    <div>
        <table style="font-weight:bold; font-size: 1.1rem; color: #7f7f7f; width: 100%;">
            <tr>
                <td>Número do Bilhete: {{ $ticket->ticketNumber }}</td>
                <td>Data da Emissão: {{ $ticket->dateF }}</td>
            </tr>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">
                    Dados Cadastrais
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="9">
                    <h5 class="title">Nome do Segurado</h5>
                    <span class="content">{{ $ticket->customer['fullName'] }}</span>
                </td>
                <td colspan="3">
                    <h5 class="title">Sexo</h5>
                    <span class="content">{{ $ticket->customer['genderFull'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <h5 class="title">RG</h5>
                    <span class="content">{{ $ticket->customer['rg'] }}</span>
                </td>
                <td colspan="6">
                    <h5 class="title">CPF</h5>
                    <span class="content">{{ $ticket->customer['cpf'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="12">
                    <h5 class="title">Endereço</h5>
                    <span class="content">{{ $ticket->customer['fullAddress'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <h5 class="title">Bairro</h5>
                    <span class="content">{{ $ticket->customer['neighborhood'] }}</span>
                </td>
                <td colspan="4">
                    <h5 class="title">Cidade</h5>
                    <span class="content">{{ $ticket->customer['city'] }}</span>
                </td>
                <td colspan="4">
                    <h5 class="title">UF</h5>
                    <span class="content">{{ $ticket->customer['state'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <h5 class="title">CEP</h5>
                    <span class="content">{{ $ticket->customer['zipCode'] }}</span>
                </td>
                <td colspan="6">
                    <h5 class="title">Telefone</h5>
                    <span class="content">{{ $ticket->customer['mainPhone'] }}</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Descrição do produto (Bem Segurado)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="6">
                    <h5 class="title">Marca</h5>
                    <span class="content">{{ $ticket->device['brand'] }}</span>
                </td>
                <td colspan="6">
                    <h5 class="title">Modelo</h5>
                    <span class="content">{{ $ticket->device['model'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <h5 class="title">IMEI</h5>
                    <span class="content">{{ $ticket->device['imei'] }}</span>
                </td>
                <td colspan="6">
                    <h5 class="title">Valor</h5>
                    <span class="content">{{ $ticket->device['price'] }}</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
                <tr>
                    <th colspan="12">Coberturas Contratadas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5">
                        <h5 class="title">Descrição</h5>
                    </td>
                    <td colspan="1">
                        <h5 class="title">LMI <small>Limites Máximos de Indenização</small></h5>
                    </td>
                    <td>
                        <h5 class="title">Franquia</h5>
                    </td>
                    <td>
                        <h5 class="title">Carência</h5>
                    </td>
                    <td>
                        <h5 class="title">Prêmio Bruto</h5>
                    </td>
                    <td>
                        <h5 class="title">IOF</h5>
                    </td>
                    <td colspan="2">
                        <h5 class="title">Prêmio Liquido</h5>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <span class="content">{{$ticket->product['label']}}</span>
                    </td>
                    <td colspan="1">
                        <span class="content">{{ $ticket->device['price'] }}</span>
                    </td>
                    <td>
                        <span class="content">{{ data_get($ticket->premium, 'franchise', 'N/A' ) }}</span>
                    </td>
                    <td>
                        <span class="content">{{ data_get($ticket->premium, 'lack', 'N/A') }}</span>
                    </td>
                    <td>
                        <span class="content">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney($ticket->premium['total']) }}</span>
                    </td>
                    <td>
                        <span class="content">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'iof') ?? 0.0) }}</span>
                    </td>
                    <td colspan="2">
                        <span class="content">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'liquid') ?? 0.0) }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Vigência das Coberturas do Seguro</th>
            </tr>
            </thead>
            <tbody>
            <tr class="content_validity">
                <td style="background-color: #d9d9d9" colspan="6">
                    <h5 class="title">Início às 24h do dia {{ \Carbon\Carbon::parse($ticket->premium['validity']['start'])->format('d/m/Y') }}</h5>
                </td>
                <td style="background-color: #d9d9d9" colspan="6">
                    <h5 class="title">Fim às 24h do dia {{ \Carbon\Carbon::parse($ticket->premium['validity']['end'])->format('d/m/Y') }}</h5>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="voucher" style="page-break-before: always">
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Prêmio Total do Seguro</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-center">
                <td colspan="4">
                    <h5 class="title">Prêmio Líquido</h5>
                </td>
                <td colspan="4">
                    <h5 class="title">IOF</h5>
                </td>
                <td colspan="4">
                    <h5 class="title">Prêmio Total</h5>
                </td>
            </tr>
            <tr class="text-center">
                <td colspan="4">
                    <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'liquid') ?? 0.0) }}</h5>
                </td>
                <td colspan="4">
                    <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'iof') ?? 0.0) }}</h5>
                </td>
                <td colspan="4">
                    <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney($ticket->premium['total']) }}</h5>
                </td>
            </tr>
            <tr>
                <td colspan="12" style="text-align: left">
                    <span class="content">Forma de Pagamento: à vista</span>
                </td>
            </tr>
            <tr>
                <td colspan="12" style="text-align: left">
                    <span class="content">Será paga a remuneração do Representante no valor de
                        {{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'representative.remuneration') ?? 0.0) }},
                        equivalente a {{ data_get($ticket->premium, 'representative.percentage') ?? 0.0 }}% sobre o prêmio de seguro líquido de IOF.<br>
                        Será paga a remuneração do Corretor no valor de {{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'representative.panoramic_remuneration') ?? 0.0) }},
                        equivalente a {{ data_get($ticket->premium, 'representative.panoramic_percentage') ?? 0.0 }}% sobre o prêmio de seguro líquido de IOF.</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Definições</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        <span class="title"><strong>Fabricante:</strong></span> Empresa que originalmente manufaturou, montou ou importou o Bem Segurado.
                    </p>
                    <p class="content">
                        <span class="title"><strong>Garantia do Fabricante:</strong></span> Garantia oferecida pelo Fabricante e prevista no Certificado de Garantia ou Manual do Produto.
                    </p>
                    <p class="content">
                        <span class="title"><strong>Garantia do Fornecedor:</strong></span> É a garantia legal e, se houver a garantia contratual originalmente oferecida pelo fornecedor, nos termos definidos pela lei.
                    </p>
                    <p class="content">
                        <span class="title"><strong>Limite Máximo de Indenização (LMI):</strong></span> valor máximo de responsabilidade da Seguradora estabelecido no Bilhete para cada cobertura contratada, a cada sinistro coberto.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Objetivo do Seguro</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        O seguro contratado tem como objetivo propiciar ao Segurado, facultativamente e mediante o pagamento de prêmio, a extensão temporal da garantia do fornecedor de um bem adquirido, e, quando contratada, sua complementação através de Cobertura Adicional, até os Limites Máximos de Indenização especificados no Bilhete, observados os Riscos Excluídos e as demais Condições Contratuais.
                        A cobertura restringe-se ao Bem Segurado mencionado no Bilhete de Seguro, que deve ser contratado na aquisição do bem ou durante a vigência da Garantia do Fornecedor, e a eventos ocorridos durante a Cobertura do Risco.
                    </p>
                    <h5 class="title">Recomenda-se que o segurado guarde o certificado de garantia do fornecedor.</h5>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Coberturas</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        O seguro de Extensão de Garantia Original, cuja vigência inicia imediatamente após o término da garantia do
                        fornecedor e perdura pelo período especificado no Bilhete, contempla as mesmas coberturas e exclusões oferecidas pela garantia do fornecedor.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Riscos Excluídos</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        Estão excluídas deste seguro quaisquer despesas ou reclamações decorrentes de, ou de algum modo relacionadas a:
                    </p>
                    <ol type="a">
                        <li>
                            Todas as situações que constarem como excluídas na garantia do fabricante e/ou fornecedor do Bem Segurado, descritas no Certificado de Garantia do bem,
                            inclusive aquelas que provocarem a perda da garantia, nos termos do item de PERDA DE DIREITO DA GARANTIA DO FORNECEDOR E PERDA DO DIREITO À INDENIZAÇÃO DO SEGURO das Condições Gerais;
                        </li>
                        <li>
                            Atos ilícitos dolosos ou culpa grave equiparável ao dolo praticado pelo Segurado, pelo beneficiário ou pelo representante, de um ou de outro.
                            No caso de pessoa jurídica esta exclusão aplica-se aos sócios controladores, aos seus dirigentes e administradores legais, aos beneficiários e
                            aos seus respectivos representantes; Danos ou perdas causados direta ou indiretamente ou como consequência de reações nucleares, radiação nuclear ou contaminação radioativa.
                        </li>
                    </ol>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="voucher" style="page-break-before: always">
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">BENS NÃO COMPREENDIDOS NO SEGURO</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">Este seguro não cobre danos sofridos por:</p>
                    <ol type="a">
                        <li>
                            Produtos que sejam utilizados em estabelecimentos comerciais ou industriais, ou ainda, para fins comerciais ou industriais,
                            para aluguel ou de qualquer maneira de uso não doméstico, exceto Condicionadores de Ar quando utilizados
                            em escritórios individuais e desde que a área refrigerada não exceda às especificações do Fabricante;
                        </li>
                        <li>
                            Bens para aluguel;
                        </li>
                        <li>Quaisquer acessórios e bens consumíveis não incluídos na Garantia do Fornecedor, tais como pilhas, baterias, cartuchos de tinta, filtros, etc;</li>
                        <li>Programas aplicativos, sistemas operacionais e softwares, sendo que a responsabilidade pela realização de
                            qualquer tipo de backup ou sua reinstalação em decorrência do conserto do Bem Segurado é única e exclusivamente do cliente;</li>
                        <li>Bens infungíveis, tais como raridades, antiguidades, coleções e quaisquer objetos cujo valor seja de cunho estimativo ou não mensurável;</li>
                        <li>Produto reserva no período de conserto do Bem Segurado com defeito;</li>
                        <li>Bens cuja aquisição não possa ser comprovada mediante apresentação de Nota ou Cupom Fiscal.</li>
                    </ol>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">PERDA DE DIREITO À INDENIZAÇÃO</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        Caso fique comprovado, mediante laudo técnico, que o Segurado perdeu o direito à garantia do fornecedor por violação às regras de garantia do fabricante, a Seguradora poderá eximir-se do pagamento da indenização do seguro de garantia estendida contratado, desde que apresente para o consumidor, por escrito e de forma clara e precisa as razões objetivas da perda da garantia. Caberá à Seguradora comprovar, por laudo técnico ou outro meio idôneo, a perda de direito a que se refere este item.
                        O Segurado perderá o direito à indenização se agravar intencionalmente o risco ou se não cumprir as recomendações do Manual do Fabricante quanto à instalação, montagem, uso, conservação e manutenção periódica e preventiva do Bem Segurado, conforme as condições nele contidas.
                        O Segurado está obrigado a comunicar à Seguradora, logo que saiba, qualquer fato suscetível de agravar o risco coberto, sob pena de perder o direito à indenização, se ficar comprovado que silenciou de má-fé.
                    </p>
                    <ol type="a">
                        <li>A Seguradora, desde que o faça-nos 15 (quinze) dias seguintes ao recebimento do aviso de agravação do risco,
                            poderá dar-lhe ciência, por escrito, de sua decisão de cancelar o contrato ou, mediante acordo entre as partes,
                            restringir a cobertura contratada ou cobrar a diferença de prêmio cabível.
                        </li>
                        <li>O cancelamento do contrato só será eficaz 30 (trinta) dias após a notificação, devendo ser restituída a diferença do prêmio,
                            calculada proporcionalmente ao período a decorrer da cobertura de risco. Sob pena de perder o direito à indenização,
                            o Segurado participará o sinistro à Seguradora, tão logo tome conhecimento, e adotará as providências imediatas para minorar suas consequências.
                        </li>
                    </ol>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="voucher" style="page-break-before: always">
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">COMO PROCEDER EM CASO DE SINISTRO</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <table class="table-info">
                        <tbody>
                        <tr>
                            <td style="background-color: #d9d9d9"><span class="bold">Aviso de Sinistro</span><br>8h às
                                20h (seg. a sex.) e 8h às 17h (sáb)
                            </td>
                            <td><p>4000 1084 Capitais e regiões Metropolitanas 0800 940 0983 Demais regiões</p></td>
                        </tr>
                        </tbody>
                    </table>
                    <p class="content">Informe ao atendente as informações necessárias e em seguida, envie as seguintes documentações para análise do sinistro:</p>
                    <ol type="a">
                        <li>Documento fiscal de aquisição do bem;</li>
                        <li>Bilhete do seguro;</li>
                        <li>CPF ou outro documento oficial de identificação do Segurado;</li>
                    </ol>
                    <p class="content">
                        Após a entrega da documentação completa, exigida e necessária para regulação do sinistro,
                        o reparo ou indenização deverá ser finalizado em até 30 (trinta) dias corridos. O início da contagem desse prazo ocorrerá:
                    </p>
                    <ol type="a">
                        <li>Na data da entrega do bem na assistência técnica ou no ponto de coleta;</li>
                        <li>Na data da comunicação do sinistro pelo Segurado, quando for necessária a retirada do bem ou o atendimento em domicílio, por representante ou empresa indicada pela Seguradora.</li>
                    </ol>
                    <p class="content">
                        A Seguradora terá o prazo de 30 (trinta) dias a partir da entra de toda a documentação exigível para o pagamento da indenização devida.
                        No caso de solicitação de documentação complementar, facultada uma única vez à Seguradora em caso de dúvida justificável, esse prazo será suspenso,
                        voltando a correr a partir do dia útil subsequente àquele em que forem completamente atendidas as exigências.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Informações Gerais</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <p class="content">
                        <strong>Ao contratar o seguro objeto deste documento, reconheço e concordo que meus dados pessoais serão utilizados para os fins necessários à
                        consecução do seu objeto, seguindo as diretrizes da Política de Privacidade da Generali.
                        Para obter mais informações sobre como a Generali cuida dos seus dados pessoais, bem como para entender
                        como você pode exercer seus direitos relacionados aos seus dados pessoais, consulte a nossa Política de Privacidade, disponível em:
                        <a href="https://www.generali.com.br">https://www.generali.com.br</a>.
                        Caso você ainda tenha dúvidas sobre esse assunto, fique à vontade para entrar em contato conosco pelo
                        e-mail <a href="privacidade@generali.com.br">privacidade@generali.com.br</a>.</strong><br>
                        A contratação do seguro é opcional, sendo facultado ao segurado o seu cancelamento a qualquer tempo, com devolução do prêmio pago
                        referente ao período a decorrer, se houver. Na ocorrência de evento coberto, caso o valor da obrigação financeira devida ao credor
                        seja menor do que o valor a ser indenizado no seguro prestamista, a diferença apurada será paga ao
                        próprio segurado ou ao segundo beneficiário indicado, conforme dispuserem as condições gerais.<br>
                        O Segurado poderá desistir do Seguro no prazo de 7 (sete) dias corridos a contar da emissão do Bilhete e exercerá seu direito de
                        arrependimento pelo mesmo meio utilizado para contratação, ou por meio do Serviço de Atendimento ao Consumidor – SAC.
                        O cancelamento do Seguro poderá ser realizado a qualquer momento, a pedido do segurado, com a devolução proporcional do prêmio pago.
                        Na hipótese de cancelamento até a data de início da cobertura de risco, a devolução do prêmio será integral com retenção dos emolumentos.
                        Após a data de início da cobertura, a devolução do prêmio pago será proporcional. Incidem as alíquotas de 0,65% de PIS/Pasep e de 4% de
                        COFINS sobre os prêmios de seguros, deduzidos do estabelecido em legislação específica.
                        O Segurado poderá consultar a situação cadastral do seu corretor de seguros no site <a href="www.susep.gov.br">www.susep.gov.br</a>, por meio do
                        número de seu registro na SUSEP, nome completo, CNPJ ou CPF. As condições contratuais do Plano de Seguro a que este bilhete está vinculado encontram-se
                        registradas na SUSEP de acordo com o número de Processo SUSEP e poderão ser consultadas no endereço eletrônico www.susep.gov.br ou no site <a href="www.generali.com.br">www.generali.com.br</a>
                        O registro deste plano na SUSEP não implica, por parte da Autarquia, incentivo ou recomendação à sua comercialização. Serviço de Atendimento ao Público SUSEP: 0800-0218484.
                        Se desejar a reavaliação da solução apresentada, ligue para Ouvidoria: 0800 880 3900 de segunda à sexta das 09h00 às 18h00 ou preencha o formulário no site, na área de Ouvidoria.
                        Este material contém apenas um resumo do seu seguro, consulte a íntegra das Condições Gerais no site: www.generali.com.br.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="voucher" style="page-break-before: always">
    <div>
        <table class="table-area">
            <thead>
            <tr>
                <th colspan="12">Informações Gerais (CONTINUAÇÃO)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="12">
                    <table class="table-info">
                        <thead>
                        <tr>
                            <th colspan="4" style="background-color: #bfbfbf">Informações institucionais, Cancelamento, e Reclamações - 24 horas, 7 dias por semana.
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="background-color: #d9d9d9">SAC</td>
                            <td>0800 889 0200</td>
                            <td style="background-color: #d9d9d9">
                                <span class="bold">Atendimento Deficiente Auditivo</span>
                            </td>
                            <td>0800 889 0400</td>
                        </tr>
                        </tbody>
                    </table>
                    <p class="content"><span class="bold">Seguradora: </span> GENERALI BRASIL SEGUROS S.A. - <span class="bold">Código de Registro SUSEP: </span> 0590-8</p>
                    <p class="content"><span class="bold">Representante: </span> Trade-up Serviços de Apoio Administrativo e Comercio de Equipamentos de Telefonia e Comunicação LTDA -
                        E COMÉRCIO DE EQUIPAMENTOS DE TELEFONIA E COMUNICAÇÃO LTDA. - <span class="bold">CNPJ: </span> 22.696.923/0001-62</p>
                    <p class="content"><span class="bold">Corretora: </span> Panorâmica Corretora de Seguros LTDS - EPP <span class="bold">Código de Registro SUSEP: </span> 10.2040615.0</p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="width: 100%;">
        <table class="signatures">
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center"><img style="height: 40px"
                                                    src="{{$layout->getImage('signature', 'jpeg')}}" alt="Assinatura">
                </td>
                <td></td>
            <tr>
                <td></td>
                <td class="signature">
                    <span>
                        {{ $ticket->customer['fullName'] }}<br>
                        {{ $ticket->city }}, {{ $ticket->date }}
                    </span>
                </td>
                <td></td>
                <td class="signature">
                    <span>
                        Andrea Crisanaz<br>
                    Presidente Generali Brasil Seguros
                    </span>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>