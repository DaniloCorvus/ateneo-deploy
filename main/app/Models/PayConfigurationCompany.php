<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayConfigurationCompany extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'affect_transportation_assistance' => 'boolean',
        'vacations_31_pay' => 'boolean'
    ];

    protected $columns = ['id', 'disability_percentage_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function percentage()
    {
        return $this->belongsTo(DisabilityPercentage::class);
    }

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
