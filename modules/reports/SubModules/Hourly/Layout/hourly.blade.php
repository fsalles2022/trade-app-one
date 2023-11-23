<head>
    <style>
        table {
            border-spacing: 0pt;
            margin: 0;
            padding: 0;
            width: 100%;
            letter-spacing: .4pt;
            font-family: sans-serif;
            font-size: 8px;
            color: rgba(0, 0, 0, .8);
            text-align: center;
            /*page-break-inside: avoid !important;*/
            /*transform: scale(.95);*/
        }

        table th {
            page-break-after: auto !important;
        }

        table tr:nth-of-type(even) > th {
            background-color: rgba(0, 0, 0, .05);
        }

        table .tr-header > th {
            background: rgba(0, 0, 0, .8);
        }

        th {
            font-weight: 500;
        }

        .tr-header {
            color: white;
        }

        .tr-header th {
            border-top: 1pt solid white;
            border-left: 1pt solid white;
            padding: 4pt 0;
        }

        .separator th {
            border-top: 1pt solid lightgray;
            background: white;
            color: white;
        }

        .tr-list th {
            border-top: 1pt solid lightgray;
            border-left: 1pt solid lightgray;
            padding: 3pt 0;
        }

        .tr-list th:last-of-type {
            border-right: 1pt solid lightgray;
        }

        .tr-list:last-child th {
            border-bottom: 1pt solid lightgray;
        }

        .tr-list th:first-child {
            font-weight: 60;
        }

        .text-center {
            text-align: center;
            font-family: sans-serif;
        }

        .network {
            margin-top: -20pt;
            text-transform: uppercase;
            width: 100%;
            display: inline-block;
            color: rgba(0, 0, 0, .7);
        }

        .title {
            color: rgba(0, 0, 0, .54);
            margin-bottom: 32pt;
            margin-top: 0pt;
            text-align: left;
            font-size: 13px;
        }

        .first.tr-list th {
            background-color: rgba(255, 0, 0, .05);
            border-bottom: 1pt solid red;
        }

        .logo {
            max-height: 80px;
            position: fixed;
            max-width: 360px;
            width: auto;
            margin-top: -8pt;
        }
    </style>
</head>

<section>
    {{--<img--}}
            {{--class="logo"--}}
            {{--src="{{ base_path('modules/reports/SubModules/Hourly/Layout/logo.svg') }}"--}}
            {{--alt=""--}}
    {{-->--}}

    <h2 class="title text-center"> {{ $report->network }} - VENDAS ACUMULADAS ATÉ: {{ $report->date }} -  {{ $report->time }}</h2>

    <table width="100%">
        <tr class="tr-header">
            <?php $report->fillHeaderGroupRow() ?>
        </tr>

        <tr class="tr-header">
            <?php $report->fillHeaderTitleRow() ?>
        </tr>

        <tr class="tr-header">
            <?php $report->fillHeaderRow('PERCENT') ?>
        </tr>

        <tr class="tr-header">
            <?php $report->fillHeaderRow('QUANTITY') ?>
        </tr>

        @foreach($report->blocks as $blockName => $block)

            <tr class="separator">
                <th colspan="{{ $report->totalColumns}}"> -</th>
            </tr>

            <tr class="tr-header">
                <?php $report->fillHeaderGroupRow() ?>
            </tr>

            <tr class="tr-header">
                <?php $report->fillHeaderTitleRow(1) ?>
            </tr>

            <tr class="first tr-list">
                <?php $report->fillBodyRow($block['RESUME'], $blockName) ?>
            </tr>

            @foreach($block['DETAILS'] as $rowName => $row)
                <tr class="tr-list">
                    <?php $report->fillBodyRow($row, $rowName) ?>
                </tr>
            @endforeach

        @endforeach

    </table>
</section>