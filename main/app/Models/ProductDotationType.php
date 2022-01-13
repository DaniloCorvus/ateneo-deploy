<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> cad4ec93f5b5bac9371b86520ae0a632e88ced6e
use Illuminate\Database\Eloquent\Model;

class ProductDotationType extends Model
{
<<<<<<< HEAD
    //
=======


    protected $fillable = ['name'];


    public function inventary(){
        return $this->hasMany(InventaryDotation::class)->where('stock','>','0');
    }
>>>>>>> cad4ec93f5b5bac9371b86520ae0a632e88ced6e
}
