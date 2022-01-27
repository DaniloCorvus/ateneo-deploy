<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariableProduct extends Model
{
    protected $fillable =
    [
        'product_id',
        'subcategory_variables_id',
        'valor'

    ];
}
