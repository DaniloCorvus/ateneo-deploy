<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WinningList extends Model
{

    protected $fillable = [
        'code',
        'name',
        'percentage'
    ];
}
