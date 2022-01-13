<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arl extends Model
{
    protected $table = 'arl';
    protected $fillable = [
        'name', 'nit', 'accounting_account', 'status'
    ];
}
