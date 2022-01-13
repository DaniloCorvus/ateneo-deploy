<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryProcess extends Model
{
    protected $fillable  = [
        'person_id',
        'process_description',
        'date_of_admission',
        'date_end',
        'status',
        'file'
    ];
}
