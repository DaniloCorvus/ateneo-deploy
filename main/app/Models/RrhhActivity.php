<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RrhhActivity extends Model
{


    protected $fillable = [
        'name',
        'user_id',
        'date_start',
        'date_end',
        'rrhh_activity_type_id',
        'state',
        'description',
        'dependency_id',
        'hour_start',
        'hour_end',
    ];


    public function peopleActivity()
    {
        return $this->hasMany(RrhhActivityPerson::class);
    }
}
