<table>
    <thead>
        <tr>
            <th> Consecutivo </th>
            <th> Tipo documento </th>
            <th> Documento </th>
            <th> Nombre </th>
            <th> Cumple </th>
            <th> Sexo </th>
            <th> Telefono </th>
            <th> Direccion </th>
            <th> Municipio </th>
            <th> Departamento </th>
            <th> Eps </th>
            <th> Regimen </th>
            <th> Lugar </th>
            <th> Fecha Cita </th>
            <th> Fecha Agendado </th>
            <th> Asigna </th>
            <th> Estado </th>
            <th> Doctor </th>
            <th> Consulta </th>
            <th> Especialidad </th>
            <th> Cup </th>
            <th> Cup name </th>
            <th> Diagnostico </th>
            <th> Ips remisora </th>
            <th> Profesional remisor </th>
            <th> Especialidad remisor </th>
            <th> Estado </th>
            <th> Fecha de cancelado </th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $datum)
        <tr>
            <td> {{ $datum->consecutivo }} </td>
            <td> {{ $datum->tipo_documnto }} </td>
            <td> {{ $datum->identifier }} </td>
            <td> {{ $datum->nombre }} </td>
            <td> {{ $datum->cumple }} </td>
            <td> {{ $datum->sexo }} </td>
            <td> {{ $datum->telefono }} </td>
            <td> {{ $datum->direccion }} </td>
            <td> {{ $datum->municipio }} </td>
            <td> {{ $datum->departamento }} </td>
            <td> {{ $datum->eps }} </td>
            <td> {{ $datum->regimen }} </td>
            <td> {{ $datum->lugar }} </td>
            <td> {{ $datum->fecha_cita }} </td>
            <td> {{ $datum->created_at }} </td>
            <td> {{ $datum->asigna }} </td>
            <td> {{ $datum->estado }} </td>
            <td> {{ $datum->doctor }} </td>
            <td> {{ $datum->consulta }} </td>
            <td> {{ $datum->especialidad }} </td>
            <td> {{ $datum->cup }} </td>
            <td> {{ $datum->cup_name }} </td>
            <td> {{ $datum->diagnostico }} </td>
            <td> {{ $datum->ips_remisora }} </td>
            <td> {{ $datum->professional_remisor }} </td>
            <td> {{ $datum->speciality_remisor }} </td>
            <td> {{ $datum->state }} </td>
            <td> {{ $datum->cancellation_at }} </td>
        </tr>
        @endforeach
    </tbody>
</table>