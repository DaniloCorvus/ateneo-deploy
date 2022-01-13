<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RrhhActivityPerson extends Model
{

    protected $fillable = [
        'person_id',
        'rrhh_activity_id'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class,);
    }
}
