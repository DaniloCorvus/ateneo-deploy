<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DianAddress extends Model
{

    protected $fillable = [
        'code',
        'description'
    ];
}
