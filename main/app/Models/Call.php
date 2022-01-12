<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'llamada_id',
        'funcionario_id',
        'fecha',
        'paciente_id',
        'tramite_id',
        'ambito_id',
        'servicio_id',
        'tipificacion_id',
    ];
}
