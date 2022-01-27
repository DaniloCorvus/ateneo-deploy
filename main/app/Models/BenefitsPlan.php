<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitsPlan extends Model
{

    protected $fillable = [
        'name',
        'description',
        'status'
    ];


    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
