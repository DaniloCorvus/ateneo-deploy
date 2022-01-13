<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonifications extends Model
{

    protected $table = 'bonifications';
    protected $fillable = [
        'countable_income_id',
        'value',
        'work_contract_id',
        'status'
    ];


    public function ingreso()
    {
        return $this->belongsTo(Countable_income::class,  'countable_income_id', 'id');
    }
    public function contableIngreso()
    {
        return $this->belongsTo(Countable_income::class);
    }

    public function scopeObtener($query, Person $funcionario, $fechaInicio, $fechaFin)
    {
        $funcionario->load('contractultimate.bonifications.ingreso');
        $bonificaciones = $funcionario['contractultimate']['bonifications'];
        $data = [];
        foreach ($bonificaciones as  $bono) {
            array_push($data, (object)  $bono);
        }

        return  $funcionario['ingreso_prestacional'] = $data;
    }
}
