<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeAppointment extends Model
{

    public function subTypeAppointment()
    {
        return $this->hasMany(SubTypeAppointment::class);
    }
}
