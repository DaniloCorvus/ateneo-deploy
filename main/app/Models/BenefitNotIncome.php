<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitNotIncome extends Model
{
    protected $guarded = ['id'];


    public function ingreso()
    {
        return $this->belongsTo(Countable_income::class,'countable_income_id','id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Person::class);
    }

    public function scopeObtener($query, Person $funcionario, $fechaInicio, $fechaFin)
    {
        return $query->where('person_id',$funcionario->id)->whereBetween('created_at',[$fechaInicio,$fechaFin])->with('ingreso')->get();
    }
}
