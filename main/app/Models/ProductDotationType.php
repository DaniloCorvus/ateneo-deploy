<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDotationType extends Model
{


    protected $fillable = ['name'];


    public function inventary(){
        return $this->hasMany(InventaryDotation::class)->where('stock','>','0');
    }
}
