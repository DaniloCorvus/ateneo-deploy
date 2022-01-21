<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dotation extends Model
{

    protected $fillable=[
        'type',
        'person_id',
        'user_id',
        'dispatched_at',
        'description',
        'cost',
    ];
}
