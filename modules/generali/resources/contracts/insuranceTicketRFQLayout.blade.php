@include('sections')

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

        .indemnityList > ol > li{
            margin-left: -10px
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
            <td>
                <img alt="Logo Generali" style="height: 40px;" src="{{$layout->getImage('logo-generali2')}}">
            </td>
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
            <span style="color: #c00000; font-size: 1.9rem;">BILHETE DE SEGURO GENERALI BENS ELETRÔNICOS PORTÁTEIS</span><br>
            <span style="color: #a6a6a6; font-size: 1.5rem">Ramo de Seguro: Riscos Diversos (0171) -Processo SUSEP: 15414.900446/2016-17</span>
        </p>
    </div>
    <div>
        <table style="font-weight:bold; font-size: 1.1rem; color: #7f7f7f; width: 100%;">
            <tr>
                <td>Número do Bilhete: {{ $ticket->ticketNumber }}</td>
                <td>Data da Emissão: {{ \Carbon\Carbon::parse($ticket->premium['validity']['start'])->format('d/m/Y') }}</td>
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
                    <th colspan="12">BEM ELETRÔNICO PORTÁTIL SEGURADO</th>
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
                    <td colspan="9">
                        <h5 class="title">Descrição</h5>
                    </td>
                    <td colspan="1">
                        <h5 class="title">LMI <small>Limites Máximos de Indenização</small></h5>
                    </td>
                    <td>
                        <h5 class="title">Franquia</h5>
                    </td>
                    <td>
                        <h5 class="title">Prêmio por Cobertura (R$)</h5>
                    </td>
                </tr>
                @foreach($ticket->premium['perCoverage'] as $coverage)
                    <tr>
                        <td colspan="9">
                            <span class="content">{{$coverage['label']}}</span>
                        </td>
                        <td colspan="1">
                            <span class="content">{{ $ticket->device['price'] }}</span>
                        </td>
                        <td>
                            <span class="content">{{ $coverage['franchise'] . '% valor do LMI' }}</span>
                        </td>
                        <td>
                            <span class="content">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney($coverage['price']) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table-area">
            <tbody>
                <tr>
                    <td colspan="12">
                        <p class="content">
                            <strong>ATENÇÃO:<br>
                                FURTO SIMPLES SEM EMPREGO DE VIOLÊNCIA OU DESAPARECIMENTO INEXPLICÁVEL OU
                                SIMPLES EXTRAVIO NÃO ESTÃO COBERTOS NESTE CONTRATO DE SEGURO.</strong>
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
                    <th colspan="12">PRÊMIO DO SEGURO</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td colspan="2">
                        <h5 class="title">Valor Líquido</h5>
                    </td>
                    <td colspan="2">
                        <h5 class="title">IOF</h5>
                    </td>
                    <td colspan="3">
                        <h5 class="title">Valor Total</h5>
                    </td>
                    <td colspan="3">
                        <h5 class="title">Forma de Pagamento</h5>
                    </td>
                    <td colspan="2">
                        <h5 class="title">Periodicidade</h5>
                    </td>
                </tr>
                <tr class="text-center">
                    <td colspan="2">
                        <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'liquid') ?? 0.0) }}</h5>
                    </td>
                    <td colspan="2">
                        <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney(data_get($ticket->premium, 'iof') ?? 0.0) }}</h5>
                    </td>
                    <td colspan="3">
                        <h5 class="title">{{ \TradeAppOne\Domain\Components\Helpers\MoneyHelper::formatMoney($ticket->premium['total']) }}</h5>
                    </td>
                    <td colspan="3">
                        <h5 class="title">Cartão de crédito</h5>
                    </td>
                    <td colspan="2">
                        <h5 class="title">{{ $ticket->payment > 1 ? ' Parcelado' : ' Único' }}</h5>
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
                            <span class="title"><strong>Franquia:</strong></span> É um valor pelo qual o segurado fica responsável como segurador de si mesmo.
                        </p>
                        <p class="content">
                            <span class="title"><strong>Furto Qualificado Mediante Arrombamento:</strong></span> Subtrair, para si ou para outrem, coisa alheia móvel com destruição ou rompimento de
                            obstáculo à subtração da coisa.
                        </p>
                        <p class="content">
                            <span class="title"><strong>LMI ou Importância Segurada::</strong></span> É o valor máximo de responsabilidade por parte da seguradora, de cada cobertura contratada. Os limites de
                            cada cobertura são independentes, não se somando nem se comunicando.
                        </p>

                       @yield('definitions_' . data_get($ticket->product, 'slug', 'rf_qa_tradeup'))

                        <p class="content">
                            <span class="title"><strong>Roubo:</strong></span> Subtração de coisa móvel alheia, para si ou para outrem, mediante grave ameaça violência à pessoa, ou depois de havê-la, por
                            qualquer meio, reduzido à impossibilidade de resistência.
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
                            O seguro contratado tem por objetivo garantir o pagamento de indenização por prejuízos aos bens eletrônicos portáteis segurados em
                            consequência de riscos previstos nas coberturas contratadas, desde que respeitado o Limite Máximo de Indenização e observadosos riscos
                            excluídos.
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
                    <th colspan="12">Riscos Excluidos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12">
                        <p class="content">
                            Este seguro não indenizará os eventos abaixo e suas consequências:
                        </p>
                        <ol type="a">
                            <li>
                                Quaisquer ocorrências, falhas ou defeitos pré-existentes à data de início de vigência das coberturas contratadas;
                            </li>
                            <li>
                                Qualquer tipo de responsabilidade do fabricante ou do fornecedor do bem segurado, legal ou contratual;
                            </li>
                            <li>
                                Atendimentos e avaliações técnicas de bens que não apresentem defeitos decorrentes dos riscos cobertos por este seguro;
                            </li>
                            <li>
                                Aluguel ou empréstimo de um bem reserva no período de conserto ou reposição do bem segurado;
                            </li>
                            <li>
                                Instalação e configuração de programas (“softwares”) de qualquer tipo, ou sua reinstalação em decorrência da substituição ou do
                                conserto do aparelho eletrônico segurado;
                            </li>
                            <li>
                                Falhas, defeitos ou mau funcionamento, inclusive quando causados por programas (softwares) ou sistemas de qualquer tipo,
                                originais ou não;
                            </li>
                            <li>
                                Danos ou perdas que sejam consequência direta do funcionamento, desgaste pelo uso, corrosão, ferrugem, umidade ou
                                deterioração gradual consequente das condições atmosféricas, químicas, térmicas ou mecânicas, má qualidade, vício próprio,
                                desarranjo mecânico ou eletrônico, ou defeitos de fabricação;
                            </li>
                            <li>
                                Operações de reparo, ajustamento e serviços de manutenção não relacionados aos riscos cobertos;
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
                    <th colspan="12">Riscos Excluidos (CONTINUAÇÃO)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12">
                        <ol type="a" start="9">
                            <li>
                                Utilização ou operação como meio de causar prejuízo, de qualquer computador ou programa, sistema ou vírus de computador, ou
                                ainda, de qualquer outro sistema ou fraude eletrônica;
                            </li>
                            <li>
                                Lucros cessantes, perda de receita, perda de informações de agenda e contatos e outros prejuízos causados pela falta ou pela
                                paralisação parcial ou total do bem segurado;
                            </li>
                            <li>
                                Apropriação ou destruição por força de regulamentos alfandegários;
                            </li>
                            <li>
                                Ação ou omissão do Segurado, de seu representante ou seus familiares, bem como de quaisquer pessoas que com ele convivam
                                permanente ou temporariamente ou dele dependam economicamente, causados por ato intencional ou má-fé, inclusive negligência
                                em usar de todos os meios comprovadamente ao seu alcance para evitar os prejuízos cobertos, durante ou após a ocorrência de
                                qualquer sinistro;
                            </li>
                            <li>
                                Atos ilícitos dolosos ou culpa grave equiparável ao dolo praticados pelo Segurado, pelo beneficiário ou pelo representante, de um
                                ou de outro. No caso de pessoa jurídica esta exclusão aplica-se aos sócios controladores, aos seus dirigentes, administradores
                                legais ou beneficiários ou seus respectivos representantes;
                            </li>
                            <li>
                                Tumulto, greve ou lock-out (cessação da atividade por ato ou fato do empregador);
                            </li>
                            <li>
                                Guerra ou invasão, atos de inimigos estrangeiros, atos de hostilidade (com ou sem declaração de guerra), guerra civil, rebelião ou
                                revolução, insurreição, poder militar usurpante ou usurpado ou atividades maliciosas de pessoas a favor de ou em ligação com
                                qualquer organização política, motim, confisco, nacionalização, comando, requisição ou destruição ou dano aos bens segurados
                                pela perturbação da ordem política ou social do país ou por ato de qualquer autoridade de fato ou de direito, civil ou militar;
                            </li>
                            <li>
                                Ato terrorista, cabendo à Seguradora, neste caso, comprovar com documentação hábil, acompanhada de laudo circunstanciado
                                que caracterize a natureza do atentado, independentemente de seu propósito e desde que tenha sido devidamente reconhecido
                                como atentatório à ordem pública pela autoridade pública competente;
                            </li>
                            <li>
                                Reações nucleares, radiação nuclear ou contaminação radioativa, arma química, biológica, bioquímica ou eletromagnética;
                            </li>
                            <li>
                                Danos morais;
                            </li>
                            <li>
                                Erro na interpretação de datas por equipamentos eletrônicos, ficando excluído qualquer prejuízo, dano, destruição, perda e/ou
                                reclamação de responsabilidade, de qualquer espécie, natureza ou interesse, desde que devidamente comprovado pela
                                Seguradora, que possa ser, direta ou indiretamente, originado de, ou consistir em falha ou mau funcionamento de qualquer
                                equipamento e/ou programa de computador e/ou sistema de computação eletrônica de dados em reconhecer e/ou corretamente
                                interpretar e/ou processar e/ou distinguir e/ou salvar qualquer data como a real e correta data de calendário, ainda que continue a
                                funcionar corretamente após aquela data; qualquer ato, falha, inadequação, incapacidade, inabilidade ou decisão do Segurado ou
                                de terceiro, relacionado com a não utilização ou não disponibilidade de qualquer propriedade ou equipamento de qualquer tipo,
                                espécie ou qualidade, em virtude do risco de reconhecimento, interpretação ou processamento de datas de calendário. Para todos
                                os efeitos, entende-se como equipamento ou programa de computador os circuitos eletrônicos, microchips, circuitos integrados,
                                microprocessadores, sistemas embutidos, hardwares (equipamentos computadorizados), softwares (programas residentes em
                                equipamentos computadorizados), programas, computadores, equipamentos de processamento de dados, sistemas ou
                                equipamentos de telecomunicações ou qualquer outro equipamento similar, sejam eles de propriedade do Segurado ou não.
                            </li>
                        </ol>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@yield('excluded_risks_' . data_get($ticket->product, 'slug', 'rf_qa_tradeup'))

<div class="voucher" style="page-break-before: always">
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
                            A Seguradora não pagará qualquer indenização e não fará a reposição ou reparação do
                            bem segurado com base no presente seguro, caso haja por parte do Segurado:
                        </p>
                        <ol type='a' class="indemnityList">
                            <li>
                                Inobservância das obrigações convencionadas neste seguro e na lei, inclusive a de comunicar
                                à seguradora, logo que saiba, qualquer fato suscetível de agravar o risco coberto pelo Bilhete
                                de Seguro, se comprovado que silenciou de má fé;
                            </li>
                            <li>
                                Dolo, fraude ou tentativa de fraude, simulação ou culpa grave para obter ou majorar a indenização;
                            </li>
                            <li>
                                O segurado agravar intencionalmente o risco objeto do Seguro.
                            </li>
                        </ol>
                        <p class="content">
                            Sob pena de perder o direito à indenização, o segurado participará o sinistro à Seguradora, tão
                            logo tome conhecimento, e adotará as providências imediatas para minorar suas consequências. A Seguradora,
                            desde que o faça nos 15 (quinze) dias seguintes ao recebimento do aviso de agravação do risco, poderá dar-lhe
                            ciência, por escrito, de sua decisão de cancelar o seguro ou, mediante acordo entre as partes, restringir a cobertura contratada.
                            O cancelamento do Seguro só será eficaz 30 (trinta) dias após a notificação, devendo ser restituída
                            a diferença do prêmio, calculada proporcionalmente ao período a decorrer.
                            Na hipótese de continuidade do seguro, a Seguradora poderá cobrar a diferença de prêmio cabível.
                            Em qualquer das hipóteses acima não haverá restituição de prêmio, ficando a Seguradora isenta de quaisquer responsabilidades.
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
                    <th colspan="12">COMO PROCEDER EM CASO DE SINISTRO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12">
                        <p class="content">
                            Para acionar o Seguro Generali Bens Eletrônicos Portáteis tenha em mãos este documento e ligue para a Central de Atendimento:
                        </p>
                        <table class="table-info">
                            <tbody>
                                <tr>
                                    <td style="background-color: #d9d9d9"><span class="bold">Aviso de Sinistro</span><br>8h às
                                        20h (seg. a sex.) e 8h às 17h (sáb)
                                    </td>
                                    <td>
                                        <p>
                                            4000 1084 Capitais e regiões Metropolitanas<br>
                                            0800 940 0983 Demais regiões
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="content">
                            O segurado deverá apresentar os seguintes documentos:
                        </p>
                        <ol type="a">
                            <li>
                                Comunicação de Sinistro através do Formulário de Aviso de Sinistro contendo os detalhes sobre a causa e consequência do evento;
                            </li>
                            <li>
                                Reclamação dos prejuízos, descrevendo os bens atingidos, quantidade e valores;
                            </li>
                            <li>
                                RG e CPF do Segurado, nos casos de pessoa física ou cópia do cartão do CNPJ em caso de pessoa jurídica;
                            </li>
                            <li>
                                Comprovante de endereço.;
                            </li>
                            <li>
                                Em caso de Roubo ou Furto qualificado, Boletim de Ocorrência Policial ou documento equivalente quando o sinistro ocorrer no exterior.
                            </li>
                            <li>
                                Protocolo de cancelamento do IMEI junto a ANATEL;
                            </li>
                            <li>
                                Relação de outros seguros ou declaração de inexistência de outros seguros garantindo os mesmos riscos cobertos por este seguro;
                            </li>
                            <li>
                                Termo de doação, com firma reconhecida (quando o caso exigir).
                            </li>
                        </ol>
                        <p class="content">
                            A Seguradora terá o prazo de 30 (trinta) dias a partir da entrega de toda documentação exigível para o pagamento da indenização
                            devida. No caso de solicitação de documentação complementar, facultada uma única vez à Seguradora em caso de dúvida
                            justificável, esse prazo será suspenso, voltando a correr a partir do dia útil subsequente àquele em que forem completamente
                            atendidas as exigências.
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
                    <th colspan="12">Informações Gerais</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12">
                        <p class="content">
                            O Segurado poderá desistir do Seguro no prazo de 7 (sete) dias corridos a contar da emissão do Bilhete e exercerá seu direito
                            dearrependimento pelo mesmo meio utilizado para contratação, ou por meio do Serviço de Atendimento ao Consumidor –SAC. O
                            cancelamento do Seguro poderá ser realizado a qualquer momento, a pedido do segurado, com a devolução proporcional do prêmio pago.
                            Na hipótese de cancelamento até a data de início da cobertura de risco, a devolução do prêmio será integral com retenção dos
                            emolumentos. Após a data de início da cobertura, a devolução do prêmio pago será proporcional. Incidem as alíquotas de 0,65% de
                            PIS/Pasep e de 4% de COFINS sobre os prêmios de seguros, deduzidos do estabelecido em legislação específica.
                            O Segurado poderá consultar a situação cadastral do seu corretor de seguros no site <a href="www.susep.gov.br">www.susep.gov.br</a>, por meio do número de seu registro
                            na SUSEP, nome completo, CNPJ ou CPF. As condições contratuais do Plano de Seguro a que este bilhete está vinculado encontram-se
                            registradas na SUSEP de acordo com o número de Processo SUSEP e poderão ser consultadas no endereço eletrônico <a href="www.susep.gov.br">www.susep.gov.br</a>
                            ou no site <a href="www.generali.com.br">www.generali.com.br</a>. O registro deste plano na SUSEP não implica, por parte da Autarquia, incentivo ou recomendação àsua
                            comercialização. Serviço de Atendimento ao Público SUSEP: 0800-0218484.
                        </p>
                        <p>
                            <strong>
                                Ao contratar o seguro objeto deste documento, reconheço e concordo que meus dados pessoais serão utilizados para os fins
                                necessários à consecução do seu objeto, seguindo as diretrizes da Política de Privacidade da Generali.
                                Para obter mais informações sobre como a Generali cuida dos seus dados pessoais, bem como para entender como você pode
                                exercer seus direitos relacionados aos seus dados pessoais, consulte a nossa Política de Privacidade, disponível em:
                                <a href='https://www.generali.com.br'>https://www.generali.com.br.</a>
                                Caso você ainda tenha dúvidas sobre esse assunto, fique à vontade para entrar em contato conosco pelo e-mail:
                                <a href='privacidade@generali.com.br'>privacidade@generali.com.br</a>
                            </strong>
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
                    <th colspan="12">Informações Gerais (Continuação)</th>
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
                        <p class="content">
                            Se desejar a reavaliação da solução apresentada, ligue para Ouvidoria: 0800 88 03 900 de segunda à sexta das 09h00 às 18h00 ou
                            preencha o formulário no site, na área de Ouvidoria.
                        </p>
                        <p class="content"><span class="bold">Seguradora: </span> GENERALI BRASIL SEGUROS S.A. - <span class="bold">Código de Registro SUSEP: </span> 0590-8</p>
                        <p class="content"><span class="bold">Representante: </span> Trade-up Serviços de Apoio Administrativo e Comercio de Equipamentos de Telefonia e Comunicação LTDA -
                            E COMÉRCIO DE EQUIPAMENTOS DE TELEFONIA E COMUNICAÇÃO LTDA. - <span class="bold">CNPJ: </span> 22.696.923/0001-62</p>
                        <p class="content"><span class="bold">Corretora: </span> Panorâmica Corretora de Seguros LTDS - EPP <span class="bold">Código de Registro SUSEP: </span> 10.2040615.0</p>
                        <p class="content">
                            Este material contém apenas um resumo do seu seguro, consulte a íntegra das Condições Gerais no site: <a href="www.generali.com.br">www.generali.com.br</a>.
                        </p>
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