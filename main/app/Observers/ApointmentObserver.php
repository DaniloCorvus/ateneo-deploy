<?php

namespace App\Observers;

use App\Events\AppointmentModify;
use App\Models\Appointment;

class ApointmentObserver
{
    public function created(Appointment $appointment)
    {
        //   $appointment->link = 'https://meet.jit.si/' . $company->simbol . date("ymd",strtotime($appointment->space->hour_start )) . str_pad( $appointment->id, 7, "0", STR_PAD_LEFT);
        //   $appointment->save();
        
    }

    public function updated(Appointment $Appointment)
    {
        // event(new AppointmentModify($Appointment));
    }
}
