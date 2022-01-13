<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyPerson extends Model
{

    protected $fillable = [
        'name',
        'n_document',
        'landline',
        'cell_phone',
        'email',
        'position',
        'observation',
        'third_party_id'
    ];

}
