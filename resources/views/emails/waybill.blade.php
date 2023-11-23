Segue Romaneio: <br>
Lote: {{ $waybill->id }} <br>
Data: {{ $waybill->date->format('d/m/Y - H:i') }} <br>
Ponto de venda: {{ $waybill->pointOfSale->slug }} - {{ $waybill->pointOfSale->label }} <br>

<br>
Quantidade de aparelhos: {{ $waybill->services->count() }} <br>
