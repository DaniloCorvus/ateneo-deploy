<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategoria';

    protected $fillable = ['Nombre', 'Separable'];

}
