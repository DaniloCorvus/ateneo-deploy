<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $fillable = [
        'name',
        'number',
        'type'

    ];

    public function contracts()
    {

        return $this->hasMany(Contract::class);

    }
}
