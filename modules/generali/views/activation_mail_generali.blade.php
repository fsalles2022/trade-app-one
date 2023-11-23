<html>
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width" name="viewport"/>
    <!--[if !mso]><!-->
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <!--<![endif]-->
    <title></title>
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css"/>
    <!--<![endif]-->
    <style type="text/css">
        table, td, tr {
            vertical-align: top;
            border-collapse: collapse
        }

        * {
            line-height: inherit
        }

        a[x-apple-data-detectors=true] {
            color: inherit !important;
            text-decoration: none !important
        }

        image {
            mix-blend-mode: multiply
        }

        .block-grid {
            margin: 0 auto;
            min-width: 320px;
            max-width: 700px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            background-color: transparent;
        }

        .text-block {
            color: #7C7C7C;
            font-family: Open Sans, Helvetica Neue, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            padding: 15px 30px 20px;
        }

        .text-block__box {
            font-size: 12px;
            line-height: 1.5;
            font-family: Open Sans, Helvetica Neue, Helvetica, Arial, sans-serif;
            mso-line-height-alt: 18px;
        }

        .text-block__box > p {
            font-size: 16px;
            color: #7C7C7C;
            line-height: 1.5;
            word-break: break-word;
            mso-line-height-alt: 24px;
            margin: 10px 0;
        }

        .text-block__header {
            color: #555555;
            font-family: Open Sans, Helvetica Neue, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            padding: 15px 30px 5px;
        }

        .text-block__header > div > p {
            font-weight: bold;
            font-size: 20px;
        }

        .text-block__title {
            color: #555555;
            font-family: 'Montserrat', 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif;
            line-height: 1.2;
            padding: 10px 30px 0;
        }

        .text-block__title > div > p {
            font-size: 40px;
            line-height: 1.2;
            font-family: Montserrat, Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;
            word-break: break-word;
            mso-line-height-alt: 17px;
            margin: 0;
        }
    </style>
    <style id="media-query" type="text/css">
        @media (max-width: 720px) {
            .block-grid, .col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important
            }

            .block-grid {
                width: 100% !important
            }

            .col {
                width: 100% !important
            }

            .col > div {
                margin: 0 auto
            }

            .no-stack .col {
                min-width: 0 !important;
                display: table-cell !important
            }

            .no-stack.two-up .col {
                width: 50% !important
            }

            .no-stack .col.num6 {
                width: 50% !important
            }
        }
    </style>
</head>
<body class="clean-body">
{{--  Logo--}}
<div style="background-color:transparent;">
    <div class="block-grid two-up no-stack">
        <div class="col num6"
             style="min-width: 320px; max-width: 350px; display: table-cell; vertical-align: top; width: 350px;">
            <div style="width:100% !important;">
                <div style="border-top:0 solid transparent; border-left:0 solid transparent; border-bottom:0 solid transparent; border-right:0 solid transparent; padding-top:10px; padding-bottom:10px; padding-right: 20px; padding-left: 20px;">
                    <img alt="Image" class="left fixedwidth"
                         src="{{$message->embed($header)}}"
                         style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 200px; display: block;"
                         title="Generali"/>
                </div>
            </div>
        </div>
    </div>
</div>
{{--  Corpo--}}
<div style="background-color:#F4F4F4;">
    <div class="block-grid">
        <div class="col num12"
             style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
            <div style="width:100% !important;">
                <div class="text-block__title">
                    <div class="text-block__box">
                        <p>
                            <strong><span>Olá, <span style="color: #3d3bee;">{{ $customerFirstName . ' ' . $customerLastName }}</span>.</span></strong>
                        </p>
                    </div>
                </div>
                <div class="text-block__header">
                    <div class="text-block__box">
                        <p>
                            <strong><span>Você tomou a decisão certa ao ativar o seu seguro, {{$label}}.</span></strong>
                        </p>
                    </div>
                </div>
                <div class="text-block">
                    <div class="text-block__box">
                        <p>
                            <span>
                                A partir de agora você pode ter a certeza de que o seu dispositivo está amparado pela Generali, uma das maiores e mais tradicionais seguradoras do mundo.
                            </span>
                        </p>
                        <p><span>
                                Anexo a esse e-mail segue o seu bilhete de seguro com todos os detalhes sobre o produto que você adquiriu e informações de contato, caso precise falar com a gente.
                            </span></p>
                        <p><span>
                                Esperamos que nada aconteça mas caso você precise, conte com a nossa equipe sempre à disposição para ajudar.
                            </span></p>
                    </div>
                </div>
                <div class="text-block">
                    <div class="text-block__box">
                        <p>
                            <span>Seja bem vindo(a), estamos por aqui.</span></p>
                        <img alt="Logo Generali" width="100" src="{{$message->embed($assinatura)}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

