<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memorandum extends Model
{

    protected $fillable = [
        'person_id',
        'memorandum_type_id',
        'details',
        'file',
        'level',
        'state',
        'approve_user_id'
    ];
    protected $table = 'memorandums';
}
