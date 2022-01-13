<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{

    protected $fillable = [
        'cup_id',
        'cum',
        'price',
        'status'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function cup()
    {
        return $this->belongsTo(Cup::class);
    }
}
