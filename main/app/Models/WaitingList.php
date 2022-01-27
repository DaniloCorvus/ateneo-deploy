<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\FillPattern;

class WaitingList extends Model
{
    protected $fillable = [
        'type_appointment',
        'type_sub_appointment',
        'profesional_id',
        'speciality_id',
        'state',
        'space_date_assign',
        'appointment_id', 
        'sub_type_appointment_id', 
        'type_appointment_id', 
        'message_cancell'
    ];

    public function appointment(){
        return $this->belongsTo(Appointment::class);
    } 
    public function speciality(){
        return $this->belongsTo(Speciality::class);
    } 
    public function typeAppointment(){ 
        return $this->belongsTo(TypeAppointment::class);
    }

    public function subTypeAppointment()
    {
        return $this->belongsTo(SubTypeAppointment::class);
    }
}
