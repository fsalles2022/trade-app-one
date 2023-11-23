<head>
    <style>
        body {
            font-family: "Times New Roman", sans-serif;
            font-size: 7.6pt;
            line-height: normal;
            box-sizing: border-box;
            text-align: -webkit-center;
        }

        p.text-title {
            margin-top: 0;
            margin-right: 0;
            margin-bottom: .0001pt;
            margin-left: 0;
            font-size: 9pt;
        }

        .voucher-table {
            border-collapse: collapse;
            border: none;
            margin-top: 13px;
        }

        .voucher-table th {
            border: solid windowtext 1pt;
            text-align: center;
            background: #D0CECE;
            padding: 0 5.4pt 0 5.4pt;
        }

        .voucher-table td {
            border: solid windowtext 1pt;
            padding: 0 5.4pt 0 5.4pt;
        }
    </style>
</head>
<body lang=PT-BR>
<div class="voucher">
    <p class="text-title" align=center><b>PROGRAMA TRADE IN</b></p>
    <p class="text-title" align=center><b>TERMO DE TRANSFERÊNCIA DE PROPRIEDADE DE EQUIPAMENTO - LOJA FÍSICA</b></p>
    <table class="voucher-table" border=1 cellspacing=0 cellpadding=0>
        <tbody>
        <tr>
            <th colspan=2><b>PARTES</b>
            </th>
        </tr>
        <tr>
            <td rowspan=5><b>1. CEDENTE</b></td>
            <td>NOME/DENOMINAÇÃO: {{ $sale->customer['fullName'] }}</td>
        </tr>
        <tr>
            <td>Endereço/Sede:
                {{ $sale->customer['local'] }}, {{ $sale->customer['number'] }}
                {{ $sale->customer['neighborhood'] }} - {{ $sale->customer['city'] }}/{{ $sale->customer['state'] }}, {{ $sale->customer['zipCode']  }}
            </td>
        </tr>
        <tr>
            <td>CNPJ/MF ou CPF/MF: {{ $sale->customer['cpf'] }}</td>
        </tr>
        <tr>
            <td>E-mail: {{ $sale->customer['email'] }}</td>
        </tr>
        <tr>
            <td>Fone contato: {{ $sale->customer['mainPhone'] }}</td>
        </tr>
        <tr>
            <td rowspan=2><b>2. EQUIPAMENTO</b></td>
            <td>Modelo: {{ "{$sale->device['brand']} {$sale->device['model']} {$sale->device['color']} {$sale->device['storage']}" }}</td>
        </tr>
        <tr>
            <td>IMEI: {{ $sale->device['imei'] }}</td>
        </tr>
        <tr>
            <td><b>3. VALOR OFERTA</b></td>
            <td>R$ {{ $sale->price }},00</td>
        </tr>
        <tr>
            <td><b>4. NÚMERO DO VOUCHER</b></td>
            <td>{{ $sale->transactionId }}</td>
        </tr>
        <tr>
            <th colspan=2><b>TRANSFERÊNCIA</b></th>
        </tr>
        <tr>
            <td colspan="2"  style="border-bottom-width: 0px; text-align: justify ">
                <p>O CEDENTE, acima qualificado, cede e transfere neste ato, de forma irrevogável e irretratável, a
                    titularidade, propriedade e posse do Equipamento acima identificado, à TRADE UP GROUP SERVIÇOS DE
                    APOIO ADMINISTRATIVOS E COMERCIO DE EQUIPAMENTOS DE TELEFONIA E COMUNICAÇÃO LTDA-ME,
                    inscrita no CNPJ/MF sob o nº 22.696.923/0001-62, aqui denominada “TRADE UP GROUP”, pelo Valor da Oferta
                    acima identificado, através deste estabelecimento negociador, mediante ao imediato pagamento por
                    meio de Vale Compras (Voucher). O CEDENTE declara e atesta o quanto segue:</p>

                <p>- O Equipamento é de única e exclusiva titularidade e posse do CEDENTE e o CEDENTE detém todos os
                    poderes e autorizações para formalizar a presente transferência;</p>

                <p>- A presente transferência é realizada pelo CEDENTE de livre e espontânea vontade, aceitando de
                    forma irrevogável e irretratável a Oferta retratada no presente termo;</p>

                <p>- Com a assinatura do presente termo, o CEDENTE fica ciente que não há direito de arrependimento da
                    transação ou pedido de restituição/devolução, dando por vendido e transferida a propriedade e posse
                    do equipamento à “TRADE UP GROUP” ou a quem ela assim determinar;</p>

                <p>- Declara o CEDENTE que todas as informações apresentadas para o seu cadastro no estabelecimento
                    negociador, bem como acerca do Equipamento quando da Oferta são verdadeiras, exatas e corretas;</p>

                <p>- O CEDENTE permanece responsável por eventuais pagamentos pendentes vencidos ou vincendos incidente
                    sobre o equipamento perante terceiros;</p>

                <p>- O CEDENTE confirma a origem lícita do produto, declarando, ainda, que em momento algum o mesmo foi
                    objeto de perda, furto, roubo ou qualquer outra ocorrência. Declara, ainda, que o produto não foi
                    adquirido ou recebido em função de qualquer ação ou ato considerado criminoso ou ilícito, sendo
                    certo que possui os documentos comprobatórios acerca da procedência lícita do Equipamento, tais como
                    notas fiscais.</p>

                <p>- O CEDENTE atesta, ainda, que o equipamento não possui nenhum impedimento ou bloqueio e, em se
                    tratando de aparelho móvel, de que não possui qualquer registro prévio no sistema CEMI (Cadastro de
                    Estações Móveis Impedidas), devendo estar desbloqueado no ato da transferência, sob pena de nulidade
                    do negócio.</p>

                <p>- O CEDENTE confirma, também, que o equipamento encontra-se nas condições relatadas na avaliação,
                    conforme
                    conferência/triagem a ser realizada pela “TRADE UP GROUP” ou a quem ela assim determinar, sem apresentar dano físico
                    visível, quebras, rachaduras ou qualquer outro dano estético, oxidação ou dano causado por contato
                    líquido de qualquer natureza, que não tenha sido identificado no momento da avaliação na loja física
                    .</p>

                <p>- O CEDENTE assevera que todas as informações, imagens, arquivos, dados, senhas (“Informações”) de
                    sua titularidade e/ou de terceiros foram integralmente deletados do Equipamento;</p>

                <p>- O CEDENTE toma ciência, no ato da assinatura deste termo que, caso referidas informações, imagens,
                    arquivos, dados e senhas (“Informações”) não tenham sido removidos do Equipamento, o CEDENTE
                    autoriza, desde já, a “TRADE UP GROUP” ou a quem ela assim determinar, excluí-las de forma
                    definitiva, sem qualquer possibilidade de
                    recuperação. Fica, então, excluída qualquer possibilidade de responsabilidade da “TRADE UP GROUP” ou a quem ela assim determinar por
                    eventuais danos incorridos de qualquer natureza, em função da remoção definitiva de tais
                    Informações;</p>

                <p>- A forma de pagamento escolhida acima não poderá ser alterada em nenhuma hipótese, sendo certo que
                    o CEDENTE está ciente de todas as condições e procedimentos para a utilização do Vale Compras
                    (Voucher), especialmente que o mesmo é pessoal e intransferível e deverá, necessariamente, ser
                    utilizado, de forma imediata, após a sua emissão, no próprio estabelecimento que o emitiu;</p>

                <p>- Nenhum valor será, em hipótese alguma, pago em dinheiro/moeda nacional ao CEDENTE;</p>

                <p>- Ao receber o Valor da Oferta por meio do Vale Compras (Voucher), conforme o caso, o CEDENTE
                    confere à “TRADE UP GROUP” ou a quem ela assim determinar a mais plena, geral, irrevogável e irretratável quitação para nada mais reclamar
                    e/ou demandar em relação à transferência de propriedade de equipamento aqui entabulada.</p>

                <p>A presente cessão será regida e interpretada de acordo com a legislação brasileira vigente e a
                    omissão, de qualquer das partes, quanto ao exercício de quaisquer direitos ou prerrogativas
                    previstas neste termo, será entendida como mera tolerância, não caracterizando renúncia, novação,
                    perdão ou alteração do pactuado, bem como na desistência de exigir o cumprimento das disposições
                    aqui contidas.</p>

                <p>As partes elegem o foro da cidade de São Paulo, SP, como competente para dirimir qualquer dúvida ou
                    eventual controvérsia que possa surgir no cumprimento das obrigações aqui estipuladas, com renúncia
                    expressa de outro qualquer, por mais privilegiado que seja.</p>

                    <p style="text-align: center; margin-bottom: 0px;">{{ $sale->city }}, {{ $sale->date }}</p>
            </td>
        </tr>
        <tr style="border-bottom-width: 0px;">
            <td style="text-align: center; border-right-width: 0px;border-bottom-width: 0px;border-top-width: 0px">
                <p>__________________________________________________________</p>

                <p>{{ $sale->customer['fullName'] }}</p>
            </td>
            <td style="text-align: center;border-left-width: 0px;border-bottom-width: 0px;border-top-width: 0px">
                <p>__________________________________________________________</p>

                <p>{{ $sale->salesman }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top-width: 0; border-right: 0; border-bottom: 0; text-align: center;">
                <?php $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                echo '<img style="margin-bottom:10px;margin-right: 10px;" src="data:image/png;base64,
                ' .
                    base64_encode($generator->getBarcode(
                        $sale->price,
                        $generator::TYPE_CODABAR
                    )) . '">';
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top-width: 0; border-right: 0; text-align: center;">R$ {{$sale->price}},00</td>
        </tr>
        </tbody>
    </table>
</div>
</body>