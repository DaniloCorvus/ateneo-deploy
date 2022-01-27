<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lunch extends Model
{

    protected $fillable = [
        'user_id',
        'value',
        'state'
    ];

    public function lunchPerson()
    {
        return $this->belongsTo(LunchPerson::class);
    }
}
