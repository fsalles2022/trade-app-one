<html>
<head>
    <style rel="stylesheet">
        {{ \Illuminate\Support\Facades\File::get(base_path('public/css/waybill/waybill.css')) }}
    </style>
</head>
<body>
<h3>Transportadora: <b>{!! $htmlFormatter->getCarrier() !!}</b></h3>
{!! $htmlFormatter->format() !!}

<table>
    <thead>
    <tr class="tr-header">
        @foreach($htmlFormatter->getAvailableColumns() as $column)
            <th>{!! $column !!}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($waybill->services as $service)
        <tr class="tr-list" >
            <th> {{$service->serviceTransaction}} </th>
            <th> {{$service['device']['imei']}} </th>
            <th> {{$service['device']['label']}} </th>
            <th> {{$service['waybill']['auditor']['firstName']}} {{$service['waybill']['auditor']['lastName']}} </th>
            <th> {{$waybill->brazilianDate()}} </th>
            <th> </th>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="advisor-wrapper">
    <div class="advisor">
        <div class="advisor-line">
            __________________________________
        </div>
        <h2>Loja</h2>
    </div>

    <div class="advisor">
        <div class="advisor-line">
            __________________________________
        </div>
        <h2>{!! $htmlFormatter->getCarrier() . " Transportadora" !!}</h2>
    </div>
</div>
</body>
</html>
