<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cup extends Model
{
    protected $fillable = [
        'code',
        'description',
        'speciality',
        'nickname',
    ];

    public function specialities()
    {
        return $this->belongsToMany(Speciality::class);
    }
}
