<style>
    *{
      font-family:'Roboto', sans-serif
    }
    .page-content {
        width: 750px;
    }

    .row {
        display: inlinie-block;
        width: 750px;
    }

    /*   table {
        border: 1px solid black;
    } */

    td {
        font-size: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .section {
        padding: 10px;
        width: 100%;
        background-color: gainsboro;
    }

    .td-header {
        font-size: 10px;
        line-height: 20px;
    }

    .titular {
        /*  font-size: 11px; */
        text-transform: uppercase;
        /*   margin-bottom: 0; */
    }

    .blocks {
        width: 25%;
        display: inline-block;
        text-transform: uppercase;
    }

    .text-right {
        text-align: right;
    }
</style>

<div style="width: 100%;">
    <div class="blocks" style="width: 60%;">
        <img src="{{public_path('/assets/img/logo.png')}}" style="width:120px;" />
        <p>
            MaqMo<br>
            N.I.T.: 10001<br>
            Calle 12 #20-20 - TEL: 302323
        </p>

    </div>
    <div class="blocks" style="width: 40%; text-align: right;">
        <h4 style="margin: 0; padding: 0;" >VÍATICOS</h4>
        <h5 style="margin: 0; padding: 0;"> {{$data['created_at']}} </h5>

    </div>
</div>

<hr style="border:1px dotted #ccc; ">

<h5>
    Información del viaje
</h5>

<div style="width: 100%; font-size: 10px; ">
    <div class="blocks" style="width: 50%;">
        <strong>Funcionario:</strong>
        {{$data['person']['first_name']}} {{$data['person']['first_surname']}}
    </div>
    <div class="blocks">
        <strong>Documento:</strong>
        {{$data['person']['identifier']}}
    </div>
    <div class="blocks">
        <strong>Tipo viaje:</strong>
        {{$data['travel_type']}}
    </div>

</div>

<div style="width: 100%; font-size: 10px; ">
    <div class="blocks" style="width: 50%;">
        <strong>Fechas:</strong>
        {{$data['departure_date']}} - {{$data['arrival_date']}}
    </div>

    <div class="blocks">
        <strong>Origen:</strong>
        {{$data['origin']['name']}}
    </div>
    <div class="blocks">
        <strong>Destino:</strong>
        {{$data['destiny']['name']}}

    </div>
</div>





@if ($data['hotels'])
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="8">
                <h6>
                    Hoteles
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Hotel</th>
            <th>Dir.</th>
            <th>Tel.</th>
            <th>Acom.</th>
            <th>Noches</th>
            <th>Desayuno</th>
            <th>Resp.</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['hotels'] as $hotel )
        <tr>
            <td> {{ $hotel['name'] }} </td>
            <td style="text-align: center;"> {{ $hotel['address'] }} </td>
            <td style="text-align: center;"> {{ $hotel['phone'] }} </td>
            <td style="text-align: center;"> {{ $hotel['pivot']['accommodation'] }} </td>
            <td style="text-align: center;"> {{ $hotel['pivot']['n_night'] }} </td>
            <td style="text-align: center;"> {{ $hotel['pivot']['breakfast'] }} </td>
            <td > {{ $hotel['pivot']['who_cancels'] }} </td>
            <td style="text-align: right;"> {{ $hotel['pivot']['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


@if ( $data['expenseTaxiCities'] )
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="6">
                <h6>
                    Taxis
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Trayecto</th>
            <th>Ciudad</th>
            <th>Tipo</th>
            <th>Tarifa.</th>
            <th>Trayectos</th>
            <th>Total</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($data['expenseTaxiCities'] as $taxi )
        <tr>
            <td> {{ $taxi['taxiCity']['taxi']['route'] }} </td>
            <td style="text-align: center;"> {{ $taxi['taxiCity']['city']['name'] }} </td>
            <td style="text-align: center;"> {{ $taxi['taxiCity']['type'] }} </td>
            <td style="text-align: center;"> {{ $taxi['rate'] }} </td>
            <td style="text-align: center;"> {{ $taxi['journeys'] }} </td>
            <td style="text-align: right;"> {{ $taxi['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


@if ($data['transports'])
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Transportes
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Empresa</th>
            <th>Tipo</th>
            <th>Viaje</th>
            <th>Resp.</th>
            <th>Total</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($data['transports'] as $transport )
        <tr>
            <td> {{ $transport['company'] }} </td>
            <td style="text-align: center;"> {{ $transport['type'] }} </td>
            <td style="text-align: center;"> {{ $transport['journey'] }} </td>
            <td>  {{ $transport['ticket_payment'] }} </td>

            <td style="text-align: right;"> {{ $transport['ticket_value'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


@if ($data['feedings'])
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="font-size: 20px;" colspan="5">
                <h6>
                    Alimentación
                </h6>
            </td>
        </tr>
        <tr style="background:#c6c6c6;">
            <th>Tipo</th>
            <th>Desayuno</th>
            <th>N Días</th>
            <th>Valor</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['feedings'] as $feed )
        <tr>
            <td> {{ $feed['type'] }} </td>
            <td style="text-align: center;"> {{ $feed['breakfast'] }} </td>
            <td style="text-align: center;"> {{ $feed['stay'] }} </td>
            <td style="text-align: right;"> {{ $feed['rate'] }} </td>

            <td style="text-align: right;"> {{ $feed['total'] }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<hr style="border:1px dotted #ccc;">


<div style="width:100%;margin-top:10px;">

    <div class="blocks" style="width: 40%;">
        <table>
            <thead>
                <tr>
                    <td>
                        <div class="title">
                            <h5>Observaciones</h5>

                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{
                $data['observation']
              }}

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="blocks" style="width: 60%;">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>
                        <h6>Dolares </h6>
                    </th>
                    <th class="text-right">
                        <h6> Pesos </h6>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hospedaje</td>
                    <td class="text-right">
                        {{ $data['total_hotels_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['total_hotels_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Transporte Terrestre</td>
                    <td></td>
                    <td class="text-right">
                        {{ $data['total_transports_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Taxis</td>
                    <td class="text-right">
                        {{ $data['total_taxis_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['total_taxis_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Alimentación</td>
                    <td class="text-right">
                        {{ $data['total_feedings_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['total_feedings_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Lavanderia</td>
                    <td class="text-right">
                        {{ $data['total_laundry_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['total_laundry_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Sobre Equipaje</td>
                    <td class="text-right">
                        {{ $data['baggage_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['baggage_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td>Otros Gastos</td>
                    <td class="text-right">
                        {{ $data['other_expenses_usd'] }}
                    </td>
                    <td class="text-right">
                        {{ $data['other_expenses_cop'] }}
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Total</td>
                    <td class="text-right">
                        {{ $data['total_usd'] }}
                    </td>

                    <td class="text-right">
                        {{ $data['total_cop'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>