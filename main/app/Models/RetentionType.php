<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetentionType extends Model
{

    protected $fillable = [
        'name',
        'account_plan_id',
        'percentage',
        'description',
        'state'
    ];
}
