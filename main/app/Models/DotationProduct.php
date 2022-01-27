<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotationProduct extends Model
{

    protected $fillable = [
        'quantity',
        'inventary_dotation_id',
        'cost',
        'dotation_id',
    ];
}
