<?php

namespace App\Traits;

use App\Models\DisabilityLeave;
use App\Models\PayrollFactor;
use Carbon\Carbon;

trait PayrollFactorDates
{

    public $data;
    public $date_start;
    public $date_end;

    public function customValidate(array $request): bool
    {
        $novedad = PayrollFactor::where('person_id', $request['person_id'])->latest()->first();

        if ($novedad == null) {
            return true;
        }

        $this->data = $request;
        $this->date_start = $novedad->date_start;
        $this->date_end =  $novedad->date_end;

        if ($this->ComparateDates($request['date_start'])) {
            return false;
        }
        if ($this->ComparateDates($request['date_end'])) {
            return false;
        }

        return true;
    }



    public function ComparateDates($dateToCompare)
    {
        if (Carbon::parse($dateToCompare)->between($this->date_start,   $this->date_end)) {
            return true;
        }
        return false;
    }
}
