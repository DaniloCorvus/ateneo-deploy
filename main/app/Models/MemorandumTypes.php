<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorandumTypes extends Model
{

    protected $fillable = ['name', 'status'];
    protected $table = 'memorandum_types';
}
