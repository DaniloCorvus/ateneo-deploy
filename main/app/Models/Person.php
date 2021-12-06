<?php

namespace App\Models;

use App\Http\Controllers\LateArrivalController;
use App\Restriction;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $guarded = [''];
    protected $hidden = ['pivot'];

    protected $fillable = [
        'blood_type',
        'cell_phone',
        'compensation_fund_id',
        'date_of_birth',
        'degree',
        'direction',
        'address',
        'email',
        'eps_id',
        'first_name',
        'first_surname',
        'second_name',
        'second_surname',
        'gener',
        'identifier',
        'image',
        'marital_status',
        'number_of_children',
        'pants_size',
        'pension_fund_id',
        'phone',
        'place_of_birth',
        'severance_fund_id',
        'shirt_size',
        'title',
        'image_blob',
        'signature_blob',



        "type_document_id",
        "identifier",
        "first_name",
        "second_name",
        "second_surname",
        "first_surname",
        "birth_date",
        "marital_status",
        "company_id",
        "department_id",
        "municipality_id",
        "medical_record",
         'arl_id',
        // "ips"

        // contract: '';
        // companies: [];
        // medical_register: '';
        // person_type_id: '';
        // specialities: [];
    ];

    public function specialities()
    {
        return $this->belongsToMany(Speciality::class)->select(['id']);
        // ->withPivot('id');
    }

    public function specialties()
    {
        return $this->belongsToMany(Speciality::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }



    public function peopleType()
    {
        return $this->belongsTo(PeopleType::class);
    }



    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['first_surname'];
    }

    public function contractultimate()
    {
        return $this->hasOne(WorkContract::class)->with('position.dependency', 'work_contract_type')->orderBy('id','DESC');
    }
    public function work_contracts()
    {
        return $this->hasMany(WorkContract::class);
    }

    public function work_contract()
    {
        return $this->hasOne(WorkContract::class);
    }

    public function liquidado()
    {
        return $this->hasOne(WorkContract::class);
        //->with('cargo.dependencia.centroCosto', 'tipo_contrato')->where('liquidado', 1);
    }

    public function payroll_factors()
    {
        return $this->hasMany(PayrollFactor::class);
    }

    /**
     * una persona tiene muchas llegadas tardes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lateArrivals()
    {
        return $this->hasMany(LateArrival::class);
    }


    /**
     * Un funcionario puede tener varios diarios fijos (dias de un turno fijo) (1,2,3,4,5 ó 6 a la semana)
     *
     * @return void
     */
    public function diariosTurnoFijo()
    {
        return $this->hasMany(DiarioTurnoFijo::class);
    }

    public function diariosTurnoRotativo()
    {
        return $this->hasMany(DiarioTurnoRotativo::class);
    }
    public function diariosTurnoRotativoAyer()
    {
        return $this->hasMany(DiarioTurnoRotativo::class);
    }
    public function diariosTurnoRotativoHoy()
    {
        return $this->hasMany(DiarioTurnoRotativo::class);
    }

    public function turnoFijo()
    {
        return $this->belongsTo(FixedTurn::class);
    }

    public function horariosTurnoRotativo()
    {
        return $this->hasMany(HorarioTurnoRotativo::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function restriction()
    {
        return $this->hasMany(Restriction::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}
