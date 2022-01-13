<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteTaxi extends Model
{

    protected $fillable = [
        'route', 'city_id'
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
