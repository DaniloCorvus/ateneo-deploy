<?php

namespace App;

use App\Models\Company;
use App\Models\RegimenType;
use App\Models\TypeAppointment;
use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{

    protected $fillable = ['person_id', 'company_id'];

    public function regimentypes()
    {
        return $this->belongsToMany(RegimenType::class);
    }
    public function contracts()
    {
        return $this->belongsToMany(Models\Contract::class);
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function typeappointments()
    {
        return $this->belongsToMany(TypeAppointment::class);
    }
}
