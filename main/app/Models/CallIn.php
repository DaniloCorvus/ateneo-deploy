<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallIn extends Model
{
    protected $fillable =  [
        'Id_Llamada',
        'Identificacion_Paciente',
        'Identificacion_Agente',
        'Tipo_Tramite',
        'Tipo_Servicio',
        'Ambito',
        'type',
        'Observation',
        'status'
    ];

    public function appointment(){
        return $this->belongsTo(Appointment::class,'call_id');
    }
    
    public function patient(){
        return $this->belongsTo(Patient::class,'Identificacion_Paciente','identifier');
    }
    
    public function formality(){
        return $this->belongsTo(Formality::class,'Tipo_Tramite');
    }

    
}
