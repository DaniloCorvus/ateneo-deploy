<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    // protected $table = 'peoploetest';
    // protected $primaryKey = 'identifier';
    // public $timestamps = false;

    protected $fillable = [
        'identifier',
        'first_name',
        'full_name',
        'second_name',
        'first_surname',
        'second_surname',
        'date_of_birth',
        'place_of_birth',
        'blood_type',
        'phone',
        'cellphone',
        'email',
        'address',
        'marital_status',
        'degree_instruction',
        'title',
        'talla_pantalon',
        'talla_bata',
        'talla_botas',
        'talla_camisa',
        'image',
        'cant_sons',
        'people_type_id',
        'personId',
        'persistedFaceId',
        'sex',
        'status',
        'signature',
        'color',
        'medical_record',
        'date_last_session',
    ];

    // protected $guarded = [''];

    public function specialties()
    {
        return $this->belongsToMany(Speciality::class);
    }
    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['first_surname'];
    }

    public function peopleType()
    {
        return $this->belongsTo(PeopleType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
