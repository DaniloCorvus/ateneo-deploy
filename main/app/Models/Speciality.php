<?php

namespace App\Models;

use App\Traits\Sorted;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use Sorted;
    
    protected $fillable = [
        'code',
        'name'
    ];
}
