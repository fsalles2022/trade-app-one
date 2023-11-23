<head>
    <style>
        html,body {
            font-family: "Times New Roman", sans-serif;
            font-size: 12pt;
            line-height: normal;
        }

        .item {
            width: 49%;
            display: inline-block;
            text-align: center;
        }

        .text-title {
            text-align: center;
            margin-top: 30px;
        }

        .campo-logistico tr td{
            white-space: pre-wrap;
            padding-right: 10px;
        }

        .logo {
            top: 0;
            text-align: right;
            margin: 8px 8px;
        }

    </style>
</head>
<body lang=PT-BR>
<div class="logo"><img src="{{ $sale->getPath() }}"></div>
<div class="voucher">
    <div><p class="text-title"><b>TERMO DE TRANSFERÊNCIA DE PROPRIEDADE DO APARELHO</b></p></div>
    <div style="text-align: justify;">
        <p>Eu, {{ $sale->customer['fullName'] }}, CPF de número {{ $sale->customer['cpf'] }}, declaro ser legítimo proprietário e possuidor do aparelho {{ "{$sale->device['brand']} {$sale->device['model']} {$sale->device['color']} {$sale->device['storage']} com IMEI {$sale->device['imei']}" }}, acima mencionado, como também, declaro que adquiri licitamente este aparelho, sendo que, transfiro, neste ato, a propriedade do mesmo à iPlace – Global Distribuição de Bens de Consumo Ltda., tendo sido efetuada a exclusão de todos os dados pessoais, arquivos de imagens, arquivos de vídeo, arquivos de texto, ou qualquer outro tipo de informação, meus e/ou de terceiros. Reconheço e autorizo a iPlace, seus funcionários, seus contratados e subcontratados, empresas afiliadas e/ou subsidiárias  que,  caso seja encontrada qualquer informação, dado, arquivo de imagem, vídeo e/ou texto no aparelho, esta proceda a remoção e destruição dos arquivos, ou seja, todos os dados ou informações que venham a ser identificados, incluindo sem limitar, dados pessoais ou de terceiros serão deletados.</p>

        <p><b> Aceito e autorizo que o único tratamento que se dará à informação e/ou dados, que sejam encontrados no equipamento, será a remoção e destruição, sem a possibilidade de que os mesmos sejam recuperados, ou que a transferência de propriedade, ora efetuada, seja cancelada, não sendo possível qualquer oposição da minha parte.</b></p>

        <p>Reconheço que a minha decisão é irrevogável e irretratável e que, após a assinatura do Presente
            Termo, não me caberá requerer a devolução ou restituição do aparelho, não sendo a presente transferência de propriedade passível de arrependimento.
            Ratifico minha autorização e consentimento para o tratamento das informações e dados nos termos acima mencionados.
        </p>

        <p>O voucher deve ser utilizado no mesmo dia e na mesma loja onde o voucher foi adquirido.</p>

    </div>

    <div style="margin-top: 90px;">
        <div class="item">
            ________________________________
            <p>{{ $sale->customer['fullName'] }}<br>Cliente Assinatura</p>
        </div>

        <div class="item">
            ________________________________
            <p>Gerente / Responsável</p><br>
        </div>
    </div>

    <div><p style="text-align: center; margin-top: 20px;">{{ $sale->city }}, {{ $sale->date }}</p></div>
    <div style="margin-top:45px;margin-bottom: 20px;"><p><b>CAMPO LOGÍSTICO:</b></p></div>

    <div>
        <div class="item">
            <table class="campo-logistico">
                <tr>
                    <td>Tela Quebrada?</td>
                    <td>(    ) Sim</td>
                    <td>(    ) Não</td>
                </tr>
                <tr>
                    <td>iPhone liga?</td>
                    <td>(    ) Sim</td>
                    <td>(    ) Não</td>
                </tr>
                <tr>
                    <td>Carcaça em boas<br>condições?</td>
                    <td>(    ) Sim</td>
                    <td>(    ) Não</td>
                </tr>

            </table>
        </div>
        <div class="item">
            <p>________________________________</p>
            <p>Responsável Loja</p>
        </div>
    </div>
</div>
</body>