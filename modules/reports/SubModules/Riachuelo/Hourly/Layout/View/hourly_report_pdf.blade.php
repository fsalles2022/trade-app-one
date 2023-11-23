<!doctype html>
<html lang="en">
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

            .td-list td {
                border-top: 1pt solid lightgray;
                border-left: 1pt solid lightgray;
                padding: 3pt 0;
            }

            .td-list td:last-of-type {
                border-right: 1pt solid lightgray;
            }

            .td-list:last-child td {
                border-bottom: 1pt solid lightgray;
            }

            .td-list td:first-child {
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

            .page-break {
                page-break-before: always;
            }

            .pb {
                padding-bottom: 30px;
            }
        </style>
    </head>
    <body>
        @php
            use Reports\SubModules\Riachuelo\Hourly\Services\BaseSaleAccumulator;
        @endphp

        <h2 class="title text-center"> VENDAS ACUMULADAS DE: {{ $startDate->format('d/m/Y') }} -  {{ $startDate->format('H:i:s') }} ATÉ: {{ $endDate->format('d/m/Y') }} -  {{ $endDate->format('H:i:s') }}</h2>

        <div>
            <!-- Table by Hierarchy -->
            <table>
                <thead>
                <tr class="tr-header">
                    <th>REGIONAL</th>

                    <!-- Operadoras -->
                    @foreach($operators->getTelecommunicationOperators() as $operator)
                        <th>
                            {{$operator}}
                        </th>
                    @endforeach

                    <th>TOTAL</th>

                    <!-- Serviço de segurança -->
                    @foreach($operators->getSecurityOperators() as $operator)
                        <th>
                            {{$operator}}
                        </th>
                    @endforeach
                    <th>RESIDENCIAL</th>
                </tr>
                </thead>
                <tbody>
                @foreach($hierarchySaleAccumulatorCollection->orderByTotalVolumeAccumulator()->getSalesAccumulators() as $hierarchySaleAccumulator)
                    <tr class="td-list">
                        <td>{{$hierarchySaleAccumulator->getHierarchy()->label}}</td>

                        <!-- Operadoras -->
                        @foreach($operators->getTelecommunicationOperators() as $operator)
                            <td>
                                {{$hierarchySaleAccumulator->getTotalVolumeByOperator($operator)}}
                            </td>
                        @endforeach

                        <td>{{$hierarchySaleAccumulator->getTotalTelecommunicationOperatorsVolumeAccumulator()}}</td>

                        <!-- Serviço de segurança -->
                        @foreach($operators->getSecurityOperators() as $operator)
                            <td>
                                {{$hierarchySaleAccumulator->getTotalVolumeByOperator($operator)}}
                            </td>
                        @endforeach

                        <td>
                            {{$hierarchySaleAccumulator->getTotalVolumeByOperator(BaseSaleAccumulator::RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX)}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="tr-header">
                        <th>TOTAL</th>

                        <!-- Total Operadoras -->
                        @foreach($operators->getTelecommunicationOperators() as $operator)
                            <th>
                                {{$hierarchySaleAccumulatorCollection->getTotalByOperator($operator)}}
                            </th>
                        @endforeach

                        <th>{{$hierarchySaleAccumulatorCollection->getTotalTelecommunicationOperatorsVolumeAccumulator()}}</th>

                        <!-- Total de Serviços de segurança -->
                        @foreach($operators->getSecurityOperators() as $operator)
                            <th>
                                {{$hierarchySaleAccumulatorCollection->getTotalByOperator($operator)}}
                            </th>
                        @endforeach

                        <th>
                            {{$hierarchySaleAccumulatorCollection->getTotalByOperator(BaseSaleAccumulator::RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX)}}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="page-break"></div>
        <div>

            <!-- Table by Point of sale -->
            @foreach($pointOfSaleSaleAccumulatorCollectionList->getPointOfSaleSaleAccumulatorCollections() as $pointOfSaleSaleAccumulatorCollection)
                <table class="pb">
                    <thead>
                        <tr class="tr-header">
                            <th>{{$pointOfSaleSaleAccumulatorCollection->getHierarchy()->label}}</th>

                            <!-- Total Operadoras -->
                            @foreach($operators->getTelecommunicationOperators() as $operator)
                                <th>
                                    {{$operator}}
                                </th>
                            @endforeach

                            <th>TOTAL</th>

                            <!-- Total de Serviços de segurança -->
                            @foreach($operators->getSecurityOperators() as $operator)
                                <th>
                                    {{$operator}}
                                </th>
                            @endforeach

                            <th>RESIDENCIAL</th>
                        </tr>
                        <tr class="tr-header">
                            <th>TOTAL</th>

                            <!-- Total Operadoras -->
                            @foreach($operators->getTelecommunicationOperators() as $operator)
                                <th>
                                    {{$pointOfSaleSaleAccumulatorCollection->getTotalByOperator($operator)}}
                                </th>
                            @endforeach

                            <th>{{$pointOfSaleSaleAccumulatorCollection->getTotalTelecommunicationOperatorsVolumeAccumulator()}}</th>

                            <!-- Total de Serviços de segurança -->
                            @foreach($operators->getSecurityOperators() as $operator)
                                <th>
                                    {{$pointOfSaleSaleAccumulatorCollection->getTotalByOperator($operator)}}
                                </th>
                            @endforeach

                            <th>
                                {{$pointOfSaleSaleAccumulatorCollection->getTotalByOperator(BaseSaleAccumulator::RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX)}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pointOfSaleSaleAccumulatorCollection->orderByTotalVolumeAccumulator()->getSalesAccumulators() as $pointOfSaleSaleAccumulator)
                            <tr class="td-list">
                                <td>{{$pointOfSaleSaleAccumulator->getPointOfSale()->label}}</td>

                                <!-- Total Operadoras -->
                                @foreach($operators->getTelecommunicationOperators() as $operator)
                                    <td>
                                        {{$pointOfSaleSaleAccumulator->getTotalVolumeByOperator($operator)}}
                                    </td>
                                @endforeach

                                <td>{{$pointOfSaleSaleAccumulator->getTotalTelecommunicationOperatorsVolumeAccumulator()}}</td>

                                <!-- Total de Serviços de segurança -->
                                @foreach($operators->getSecurityOperators() as $operator)
                                    <td>
                                        {{$pointOfSaleSaleAccumulator->getTotalVolumeByOperator($operator)}}
                                    </td>
                                @endforeach

                                <td>
                                    {{$pointOfSaleSaleAccumulator->getTotalVolumeByOperator(BaseSaleAccumulator::RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX)}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </body>
</html>
