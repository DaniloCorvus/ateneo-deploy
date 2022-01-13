<?php

namespace App\Models;

use App\Models\Patient;
use App\Traits\scopesAppointment;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    use scopesAppointment;

    protected $table = 'appointments';

    protected $fillable = [

        'call_id',
        'space_id',
        'diagnostico',
        'person',
        'ips',
        'speciality',
        'date',
        'origin',
        'procedure',
        'price',
        'observation',
        'company_id',
        'location_id',
        'code',
        'on_globo',
        'professional_id',
        'profesional',
        'link',
        'globo_id',
        'message_confirm',
        'date_updated_state',
        'user_modifier'

    ];

    protected $casts = [
        'diagnosticoId' => 'array',
    ];



    function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    function space()
    {
        return $this->belongsTo(Space::class);
    }

    function cup()
    {
        return $this->belongsTo(Cup::class, 'procedure');
    }

    function cie10()
    {
        return $this->belongsTo(Cie10::class, 'diagnostico');
    }

    function callIn()
    {
        return $this->belongsTo(CallIn::class, 'call_id');
    }

    function location()
    {
        return $this->belongsTo(Location::class, 'call_id');
    }

    function setCodeAttribute($value)
    {

        $this->attributes['code'] = $value;
    }

    function setLinkAttribute($value)
    {

        $this->attributes['link'] = 'https://meet.jit.si/' . $this->attributes['code'];
    }
}
