<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    protected $fillable = [
        "identifier",
        "document_type",
        "Identificacion",
        "regimen_type",
        "affiliate_type",
        "category_affiliate",
        "firstname",
        "middlename",
        "secondsurname",
        "surname",
        "date_of_birth",
        "state",
        "level",
        "token",
        "department",
        "municipality",
        "ips_principal",
        "Direccion",
        "Sede",
        "Correo",
        "database",
        'Email',
        'Fecha_Nacimiento',
        'Id_Ciudad',
        'Id_Departamento',
        'Id_Eps',
        'Id_Nivel',
        'Id_Regimen',
        'Id_Tipo_Identificacion',
        'Identificacion',
        'Telefono',

        "document_type",
        "Identificacion",
        "regimen_type",
        "affiliate_type",
        "category_affiliate",
        "firstname",
        "middlename",
        "secondsurname",
        "surname",
        "date_of_birth",
        "state",
        "level",
        "token",
        "department",
        "municipality",
        "ips_principal",
        "Direccion",
        "Sede",
        "Correo",
        "database",

        'Email',
        'Fecha_Nacimiento',
        'Id_Ciudad',
        'Id_Departamento',
        'Id_Eps',
        'Id_Nivel',
        'Id_Regimen',
        'Id_Tipo_Identificacion',
        'Identificacion',
        'Telefono',

        'state',
        'level_id',
        'department_id',
        'contract_id',
        'municipality_id',
        'city_id',
        'ips_id',
        'eps_id',
        'sede_id',
        'address',
        'phone',
        'email',
        'ips',
        'regional_id',
        'location_id',

        /**Nuevos */
        'company_id',
        'gener',
        'municipality_id',
        'type_document_id',
        'regimen_id',
        'contract_id',
        'optional_phone'
    ];

    public function eps()
    {
        return $this->belongsTo(Administrator::class)->withDefault([
            'name' => 'Sin Eps'
        ]);
    }
    public function company()
    {
        return $this->belongsTo(Company::class)->withDefault([
            'name' => 'Sin Ips'
        ]);
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class)->withDefault([
            'name' => 'Sin Municipio'
        ]);
    }
    public function department()
    {
        return $this->belongsTo(Department::class)->withDefault([
            'name' => 'Sin Departamento'
        ]);
    }
    public function regional()
    {
        return $this->belongsTo(Regional::class)->withDefault([
            'name' => 'Sin Regional'
        ]);
    }
    public function level()
    {
        return $this->belongsTo(Level::class)->withDefault([
            'name' => 'Sin Nivel'
        ]);
    }
    public function regimentype()
    {
        return $this->belongsTo(RegimenType::class, 'regimen_id')->withDefault([
            'name' => 'Sin Regimen'
        ]);
    }
    public function typedocument()
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class)->withDefault([
            'name' => 'Sin contrato'
        ]);
    }
    public function location()
    {
        return $this->belongsTo(Location::class)->withDefault([
            'name' => 'Sin Sede'
        ]);
    }
    public function callins()
    {
        return $this->hasMany(CallIn::class);
    }
}
