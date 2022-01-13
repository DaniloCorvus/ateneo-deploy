<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    protected $fillable = [
        'name', 'type', 'country_id', 'state'
    ];

    public function routeTaxi()
    {
        return $this->hasMany(RouteTaxi::class);
    }
}
