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
            {{ $company->company_name }}<br>
            N.I.T.: {{$company->nit}}<br>
            Calle 12 #20-20 - TEL: {{ $company->phone }}
        </p>

    </div>
    <div class="blocks" style="width: 40%; text-align: right;">
        <h4 style="margin: 0; padding: 0;" >DESCARGO</h4>
        <h4 style="margin: 0; padding: 0;" >D{{$descargo->descargo_id}}</h4>
        <h5 style="margin: 0; padding: 0;"> {{ $descargo->created_at }} </h5>

    </div>
</div>
    <table cellspacing="0" cellpadding="0" style="text-transform:uppercase;margin-top:20px;">
        <tr>
            <td style="font-size:10px;width:80px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                <strong>Funcionario:</strong>
            </td>
            <td style="font-size:10px;width:460px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                {{$descargo->first_name." ".$descargo->second_name." ".$descargo->first_surname." ".$descargo->second_surname}}
            </td>
            <td style="font-size:10px;width:50px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                <strong>C.C.:</strong>
            </td>
            <td style="font-size:10px;width:50px;background:#f3f3f3;vertical-align:middle;padding:3px;">
                {{$descargo->identifier}}
            </td>
        </tr>
    </table>
    <hr style="border:1px dotted #ccc;margin-right:35px;">
<table style="margin-top:50px">    
    <tr>
        <td style="width:400px;padding-left:10px">
            <table>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;">{{$descargo->first_name." ".$descargo->first_surname}}</td>
                    <td style="width:30px;"></td>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; border-top:1px solid black; text-align:center;"></td>
                </tr>
                <tr>
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">C.C. {{number_format($descargo->identifier,0,",",".")}} </td>    
                    <td style="width:30px;"></td>    
                    <td style="width:300px;font-weight:bold;font-family:'Roboto', sans-serif; text-align:center;">Representante Legal</td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>