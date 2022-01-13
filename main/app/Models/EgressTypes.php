<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EgressTypes extends Model
{

    protected $fillable = [
        'name',
        'associated_account',
        'type',
        'status'
    ];
    protected $table = 'egress_types';
}
