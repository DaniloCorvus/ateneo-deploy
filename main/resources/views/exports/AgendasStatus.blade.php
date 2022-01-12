<table>
    <thead>
        <tr>
            <th>Espacios Totales </th>
            <th>Espacios Ocupados </th>
            <th>Espacios Disponibles </th>
            <th>Espacios Cancelados </th>
            <th>IPS </th>
            <th>Fecha inicio de agenda </th>
            <th>Fecha finalizacion de agenda  </th>
            <th>Hora creacion </th>
            <th>Especialidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $datum)
        <tr>
            <td> {{ $datum->Espacios_Totales  }} </td>
            <td> {{ $datum->Espacios_Ocupados  }} </td>
            <td> {{ $datum->Espacios_Disponibles  }} </td>
            <td> {{ $datum->Espacios_Cancelados  }} </td>
            <td> {{ $datum->company  }} </td>
            <td> {{ $datum->fecha_inicio  }} </td>
            <td> {{ $datum->fecha_finalizacion  }} </td>
            <td> {{ $datum->hora_creacion  }} </td>
            <td> {{ $datum->especialidad }} </td>

        </tr>
        @endforeach
    </tbody>
</table>