<style>
    .page-content{
    width:750px;
    }
    .row{
    display:inlinie-block;
    width:750px;
    }
    .td-header{
        font-size:15px;
        line-height: 20px;
    }
    .titular{
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 0;
    }
</style>
    <table>
    <tbody>
      <tr>
        <td style="">
        </td>
        <td class="td-header" style="font-family:'Roboto', sans-serif;">
            <img src="{{public_path('/assets/img/logo.png')}}" style="width:120px;"/>
            MaqMo<br> 
            N.I.T.: 10001<br> 
            Calle 12 #20-20<br> 
            TEL: 302323
        </td>
        <td style="width: 350px;">  </td>
        <td>
            <h4 style="font-size:18px;line-height:22px;font-family:'Roboto', sans-serif;">AMORTIZACIÓN PRESTAMO</h4> 
            <h5 style="font-size:16px;line-height:16px;font-family:'Roboto', sans-serif;"> {{$funcionario->date}} </h5>
        </td>
        <td>
            <img src="{{public_path('/assets/img/sinqr.png')}}"  style="width: 130px; max-width:100% margin-top:-10px;"/>
        </td>
      </tr>
    </tbody>
  </table>
  <hr style="border:1px dotted #ccc;width:730px;">
    <table style="background: #e6e6e6;">
        <tr style=" min-height: 200px; background: #e6e6e6;padding: 15px; border-radius: 10px; margin: 0;">
       
        <td style="font-size:11px;font-family:'Roboto',sans-serif;font-weight:bold;width:200px;padding:5px">
        Identificación Empleado:
        </td>

        <td style="font-size:11px;width:510px;padding:5px">
        {{number_format($funcionario->identifier,0,"",".")}}
        </td>
        
    </tr>
    
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-weight:bold;font-family:'Roboto', sans-serif;width:200px;padding:5px">
        Nombre Empleado:
        </td>
        <td style="font-size:11px;width:510px;padding:5px;font-family:'Roboto', sans-serif;">
        {{$funcionario->first_name ." ". $funcionario->second_name . " " . $funcionario->first_surname . " " . $funcionario->second_surname}}
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-family:'Roboto', sans-serif;font-weight:bold;width:200px;padding:5px">
        Valor Prestamo:
        </td>
        <td style="font-size:11px;width:510px;padding:5px;font-family:'Roboto', sans-serif;">
        $ {{number_format($funcionario->value,2,",",".")}}
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-family:'Roboto', sans-serif;font-weight:bold;width:200px;padding:5px">
        Interes:
        </td>
        <td style="font-size:11px;width:510px;padding:5px;font-family:'Roboto', sans-serif;">
        {{number_format($funcionario->interest,2,",",".")}}%
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-family:'Roboto', sans-serif;font-weight:bold;width:200px;padding:5px">
        Cuotas:
        </td>
        <td style="font-size:11px;width:510px;padding:5px;font-family:'Roboto', sans-serif;">
        {{$funcionario->number_fees}}
        </td>
    </tr>
    <tr style=" min-height: 200px; background: #e6e6e6; padding: 15px; border-radius: 10px; margin: 0;">
        <td style="font-size:11px;font-family:'Roboto', sans-serif;font-weight:bold;width:200px;padding:5px">
        Valor Cuota:
        </td>
        <td style="font-size:11px;width:510px;padding:5px;font-family:'Roboto', sans-serif;">
        $ {{number_format($funcionario->monthly_fee,2,",",".")}}
        </td>
    </tr>
</table>
<table style="font-size:10px;margin-top:10px;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:60px;font-family:'Roboto', sans-serif;max-width:60px;font-weight:bold;background:#cecece;;border:1px solid #cccccc;">
            Cuota
        </td>
        <td style="width:150px;font-family:'Roboto', sans-serif;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
           Fecha Descuento
        </td>
        <td style="width:140px;font-family:'Roboto', sans-serif;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Amortización
        </td>
        <td style="width:120px;font-family:'Roboto', sans-serif;max-width:120px;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Intereses
        </td>
        <td style="width:120px;font-family:'Roboto', sans-serif;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Total Cuota
        </td>
        <td style="width:120px;font-family:'Roboto', sans-serif;font-weight:bold;background:#cecece;text-align:center;border:1px solid #cccccc;">
            Saldo
        </td>
    </tr>

    @foreach ($proyecciones['Proyeccion'] as $i => $value)

<tr>
    <td style="vertical-align:center;font-size:9px;font-family:'Roboto', sans-serif;width:50px;max-width:50px;text-align:center;border:1px solid #cccccc;">
        {{($i+1)}}
    </td>
    <td style="vertical-align:center;text-align:center;font-family:'Roboto', sans-serif;font-size:9px;width:90px;border:1px solid #cccccc;">
        {{$value['Fecha']}}
    </td>
    <td style="vertical-align:center;text-align:right;font-family:'Roboto', sans-serif;font-size:9px;word-break:break-all;width:60px;max-width:60px;border:1px solid #cccccc;">
        $ {{number_format($value['Amortizacion'],2,",",".")}}
    </td>
    <td style="width:100px;max-width:100px;text-align:right;font-family:'Roboto', sans-serif;font-size:9px;word-break:break-all;border:1px solid #cccccc;">
        $ {{number_format($value['Intereses'],2,",",".")}}
    </td>
    <td style="vertical-align:center;text-align:right;font-family:'Roboto', sans-serif;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
        $ {{number_format($value['Valor_Cuota'],2,'.',',')}}
    </td>
    <td style="vertical-align:center;text-align:right;font-family:'Roboto', sans-serif;font-size:9px;text-align:right;width:75px;border:1px solid #cccccc;">
        $ {{number_format($value['Saldo'],2,'.',',')}}
    </td>
</tr>   

    @endforeach

<tr>
    <td colspan="2" style="padding:4px;font-family:'Roboto', sans-serif;text-align:right;border:1px solid #cccccc;font-weight:bold;font-size:12px">TOTALES:</td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ {{number_format($getTotalA),2,".",","}}
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ {{number_format($getTotalI),2,".",","}}
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;">
        $ {{number_format($getTotalV),2,".",","}}
    </td>
    <td style="padding:4px;text-align:right;border:1px solid #cccccc;"></td>
</tr>
</table>
<p style="margin-top:10px;font-family:'Roboto', sans-serif;">Atentamente;</p>
<table style="margin-top:50px">    
    <tr>
        <td style="width:400px;padding-left:10px">
            <table>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;">{{$funcionario->first_name." ".$funcionario->first_surname}}</td>
                    <td style="width:30px;"></td>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;"></td>
                </tr>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">C.C. {{number_format($funcionario->identifier,0,",",".")}} </td>    
                    <td style="width:30px;"></td>    
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">Representante Legal</td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>