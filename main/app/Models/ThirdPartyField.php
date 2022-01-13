<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyField extends Model
{
    protected $fillable = [
        'label',
        'name',
        'type',
        'required',
        'length',
        'state'
    ];
}
