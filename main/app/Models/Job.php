<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{


    protected $fillable = [
        'company_id',
        'title',
        'date_start',
        'date_end',
        'position_id',
        'municipality_id',
        'min_salary',
        'max_salary',
        'turn_type',
        'description',
        'education',
        'experience_year',
        'min_age',
        'max_age',
        'can_trip',
        'change_residence',
        'visa',
        'visa_type_id',
        'salary_type_id',
        'work_contract_type_id',
        'passport',
        'document_type_id',
        'conveyance',
        'code'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
    public function salary_type()
    {
        return $this->belongsTo(SalaryTypes::class,'salary_type_id','id');
    }
    public function work_contract_type()
    {
        return $this->belongsTo(WorkContractType::class,'work_contract_type_id','id');

    }
}
