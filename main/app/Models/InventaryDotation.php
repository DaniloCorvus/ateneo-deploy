<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventaryDotation extends Model
{

    protected $fillable = [
        'product_id',
        'product_dotation_type_id',
        'name',
        'code',
        'type',
        'status',
        'cost',
        'stock'

    ];

    public function dotacionProducto(){
        return $this->hasMany(DotationProduct::class);
    }
}
