<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{    
    protected $table = 'specialties';
    protected $fillable = ['name', 'code'];
}
