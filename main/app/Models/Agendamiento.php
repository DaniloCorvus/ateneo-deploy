<?php

namespace App\Models;

use App\HistoryAgendamiento;
use App\Models\Functionary;
use Illuminate\Database\Eloquent\Model;

class Agendamiento extends Model
{

    protected $fillable = [
        'type_agenda_id',
        'type_appointment_id',
        'ips_id',
        'location_id',
        'eps_id',
        'speciality_id',
        'person_id',
        'user_id',
        'date_start',
        'date_end',
        'long',
        'hour_start',
        'hour_end',
        'days',
        'pending',
        'share',
        'type',
        'regional_percent',
        'department_id'
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function subTypeAppointment()
    {
        return $this->belongsTo(SubTypeAppointment::class, 'type_appointment_id');
    }

    public function typeAppointment()
    {
        return $this->belongsTo(TypeAppointment::class, 'type_agenda_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'ips_id');
    }
    public function eps()
    {
        return $this->belongsTo(Eps::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function spaces()
    {
        return $this->hasMany(Space::class);
    }
    public function canceledSpaces()
    {
        return $this->hasMany(Space::class)->where("state", '=', 'Cancelado');
    }
    public function assignedSpaces()
    {
        return $this->hasMany(Space::class)->where("status", '=', '0')->where('state', '=', 'Activo');
    }
    public function availableSpaces()
    {
        return $this->hasMany(Space::class)->where("status", '=', '1')->where('state', '=', 'Activo');
    }
    public function history()
    {
        return $this->hasMany(HistoryAgendamiento::class);
    }

    protected $touches = ['cups'];

    public function cups()
    {
        return $this->belongsToMany(Cup::class);
    }
}
