<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcategoryVariable extends Model
{
    protected $table = 'subcategory_variables';

    protected $fillable = ['subcategory_id', 'label', 'required', 'type'];

}
