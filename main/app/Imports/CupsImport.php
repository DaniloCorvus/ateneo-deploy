<?php

namespace App\Imports;

use App\Cup;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CupsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Cup([
            'code' => $row['codigo'],
            'description' => $row['nombre_cups'],
            'speciality' => $row['especialidad'],
            'nickname' => $row['nombre_subir_a_mmedical'],
        ]);
    }
}
