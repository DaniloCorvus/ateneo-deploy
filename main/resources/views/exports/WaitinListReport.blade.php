<table>
    <thead>
        <tr>

            <th> Tipo de documento </th>
            <th> Identificacion de paciente </th>
            <th> Nombre paciente </th>
            <th> Sexo </th>
            <th> Telefono </th>
            <th> Direccion </th>
            <th> Especialidad </th>
            <th> Municipio </th>
            <th> Departamento </th>
            <th> Eps </th>
            <th> Regimen </th>
            <th> Observaciones </th>
            <th> Fecha </th>

        </tr>
    </thead>
    <tbody>
        @foreach($data as $datum)
        <tr>
            <td> {{   $datum->tipo_documnto    }} </td>
            <td> {{   $datum->patient_identifier  }} </td>
            <td> {{   $datum->patient_name  }} </td>
            <td> {{   $datum->sexo }}
            <td> {{   $datum->telefono  }} </td>
            <td> {{   $datum->direccion  }} </td>
            <td> {{   $datum->speciality }}
            <td> {{   $datum->municipio  }} </td>
            <td> {{   $datum->departamento  }} </td>
            <td> {{   $datum->eps  }} </td>
            <td> {{   $datum->regimen }}
            <td> {{   $datum->observaciones  }} </td>
            <td> {{   $datum->fecha  }} </td>

        </tr>
        @endforeach
    </tbody>
</table>