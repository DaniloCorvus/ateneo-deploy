<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TechnicNote extends Model
{
    protected $fillable = [
        "contract_id",
        "start",
        "end",
        "anio",
        "is_active"
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
