<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryAppointment extends Model
{
    protected $fillable = [
        'appointment_id', 
        'user_id', 
        'description',
        'icon'
        ];
        
    public function usuario()
    {
        return $this->belongsTo(Usuario::class,'user_id');
    }
}
