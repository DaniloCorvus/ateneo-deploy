<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskTypes extends Model
{

    protected $fillable = [
        'risk_type',
        'accounting_account',
        'status'
    ];
    protected $table = 'risk_types';
}
