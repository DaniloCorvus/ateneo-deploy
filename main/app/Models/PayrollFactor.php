<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollFactor extends Model
{


    protected $fillable = [
        'person_id',
        'disability_leave_id',
        'disability_type',
        'date_start',
        'date_end',
        'modality',
        'observation',
        'sum',
        'payback_date',
        'number_days'
    ];


    public function disability_leave()
    {
        return $this->belongsTo(DisabilityLeave::class);
    }

    public function scopeVacations($query, Person $person, $fechaInicio)
    {
        return $query->where('person_id', $person->id)
        ->where('disability_type', 'Vacaciones')->where('date_start', '>=', $fechaInicio)->with('disability_leave')->get();
    }

    public function scopeFactors($query, Person $person, $fechaFin)
    {
        $siSuma = 1;
        $anioFechaFin = Carbon::parse($fechaFin)->year;

        return $query->where('person_id', $person->id)->with('disability_leave')->whereHas('disability_leave', function ($query) use ($siSuma) {
            $query->where('disability_type', '<>', 'Vacaciones');
                // ->where('suma', $siSuma);
        })->whereYear('date_start', '=', $anioFechaFin);
    }
}
