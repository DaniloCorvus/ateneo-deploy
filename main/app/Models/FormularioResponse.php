<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormularioResponse extends Model
{
    protected $fillable = [
        'formulario_id',
        'question_id',
        'company_id',
        'sede_id',
        'response',
    ];
}
