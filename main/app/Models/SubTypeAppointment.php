<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTypeAppointment extends Model
{

    public function TypeAppointment()
    {
        return $this->belongsTo(TypeAppointment::class);
    }
    
}
