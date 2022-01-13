<?php

namespace App\Repositories;

use App\HistoryAgendamiento;
use App\Holiday;
use App\Models\Agendamiento;
use App\Models\Person;
use App\Models\Space;
use App\Models\SubTypeAppointment;
use App\Models\TypeAppointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class AgendamientoRepository
{

    public  $person;
    public   $typeAppointment;

    public function store()
    {
        $data = request()->all();

        $daySpaces = collect([]);

        $this->validating($data);

        $data['regional_percent'] = request()->get('regionalPercent', 100);
        
        $agendamiento = Agendamiento::create($data);

        $this->person = Person::find($agendamiento->person_id);
        $this->typeAppointment = TypeAppointment::find($agendamiento->type_agenda_id);

        if (isset($data['procedureId'])) $agendamiento->cups()->sync($data['procedureId']);

        $holidays = Holiday::pluck('date')->toArray();
        $agendamiento->user_id = auth()->user()->id;
        $agendamiento->department_id = Person::findOrFail(auth()->user()->person_id)['department_id'];
        $agendamiento->save();

        $conditions = [$agendamiento->type_appointment_id];
        if ($agendamiento->type_appointment_id != 2) $conditions = array_diff(SubTypeAppointment::pluck('id')->toArray(), [2]);

        $agendamientos = Agendamiento::with('spaces')->where('person_id', $agendamiento->person_id)
            ->where(function ($q) use ($agendamiento) {
                $q->whereBetween('date_start', [$agendamiento->date_start, $agendamiento->date_end])
                    ->orWhereBetween('date_end',  [$agendamiento->date_start, $agendamiento->date_end]);
            })
            ->whereIn('type_appointment_id',  $conditions)
            ->get();


        $inicio = Carbon::parse($agendamiento->date_start);
        $fin = Carbon::parse($agendamiento->date_end);

        for ($i = $inicio; $i <= $fin; $i->addDay(1)) {
            if (in_array($i->englishDayOfWeek, $agendamiento->days)) {

                if ((in_array($i->format('Y-m-d'), $holidays) && request()->get('holiday')) || !in_array($i->format('Y-m-d'), $holidays)) {

                    $hour_start = Carbon::parse($i->format('Y-m-d') . $agendamiento->hour_start);
                    $hour_end =  Carbon::parse($i->format('Y-m-d') . $agendamiento->hour_end);
                    $temp_hour = $hour_start->copy()->addMinutes($agendamiento->long);

                    if ($temp_hour->format('Y-m-d') > $i->format('Y-m-d')) {
                        $hour_end =  Carbon::parse($temp_hour->format('Y-m-d') . $agendamiento->hour_end);
                    }

                    for (
                        $space = $hour_start;
                        $space <   $hour_end;
                        $space->addMinutes($agendamiento->long)
                    ) {
                        $result = true;
                        foreach ($agendamientos as $agendamiento) {
                            foreach ($agendamiento->spaces as $myspace) {
                                if (Carbon::parse($space->copy())->betweenIncluded($myspace->hour_start, Carbon::parse($myspace->hour_end)->subSecond()) && $myspace->state == 'Activo') {

                                    $result = false;
                                    break;
                                }
                            }
                        }
                        if ($result) $daySpaces->push($space->copy());
                    }

                    $this->fillMassiveDays($daySpaces->all(), $agendamiento, self::calculatePercent($agendamiento->regional_percent, $daySpaces->count()));
                    $daySpaces = collect([]);
                }
            }
        }

        HistoryAgendamiento::create([
            'agendamiento_id' =>  $agendamiento->id,
            'user_id' => auth()->user()->id,
            'description' => 'Agendamiento creado',
            'icon' => 'ri-calendar-2-fill'
        ]);

        Log::info([
            'user' => auth()->user()->usuario,
            'agendamiento_id' =>  $agendamiento->id,
        ]);

        return true;
    }


    public function fillMassiveDays($spaces, $agendamiento, $settings)
    {
        foreach ($spaces as $date) {
            Space::create([
                "agendamiento_id" => $agendamiento->id,
                "type" => ($settings['quantity'] > 0) ? $settings['first_type'] : $settings['second_type'],
                "status" => true,
                "hour_start" => (string) $date,
                "hour_end" => (string) $date->addMinutes($agendamiento->long),
                "long" => $agendamiento->long,
                "person_id" => $agendamiento->person_id,
                "backgroundColor" => $this->person->color,
                "className" => $this->typeAppointment->icon,
                "share" => $agendamiento->share,
            ]);

            $settings['quantity']--;
        }
    }

    public function fillDdays($agendamiento, $date)
    {
        $person = Person::find($agendamiento->person_id);
        $typeAppointment = TypeAppointment::find($agendamiento->type_agenda_id);

        Space::create([
            "agendamiento_id" => $agendamiento->id,
            "status" => true,
            "hour_start" => (string) $date,
            "hour_end" => (string) $date->addMinutes($agendamiento->long),
            "long" => $agendamiento->long,
            "person_id" => $agendamiento->person_id,
            "backgroundColor" => $person->color,
            "className" => $typeAppointment->icon,
            "share" => $agendamiento->share,
        ]);
    }

    public function validating($data)
    {
        $dateStart = Carbon::parse($data["date_start"])->toDateString();
        $dateEnd = Carbon::parse($data["date_end"])->toDateString();
        $today = Carbon::now()->toDateString();

        $hourStart = date("H:i", strtotime($data["hour_start"]));
        $hourEnd = date("H:i", strtotime($data["hour_end"]));
        $hourNow = date("H:i");

        if ($today === $dateStart) {
            if ($hourNow > $hourStart) {
                throw new Exception(
                    "La hora de inicio no puede ser menor a la hora actual"
                );
            }
            if ($hourNow > $hourEnd && $dateStart == $dateEnd) {
                throw new Exception(
                    "La hora de fin no puede ser menor a la hora actual"
                );
            }
        }

        if ($hourStart > $hourEnd && $dateStart == $dateEnd) {
            throw new Exception(
                "La hora de inicio no puede ser menor a la hora de finalización"
            );
        }

        if ($today > $dateStart || $today > $dateEnd) {
            throw new Exception(
                "Las fechas no puede ser inferior a la fecha de hoy"
            );
        }

        if ($dateStart > $dateEnd) {
            throw new Exception(
                "La fecha inicial no puede ser menor  a la final"
            );
        }

        if ($dateStart > $dateEnd) {
            throw new Exception(
                "La fecha inicial no puede ser menor  a la final"
            );
        }
    }


    public static function calculatePercent($regional_percent, $count)
    {
        $resul = floor(($regional_percent * $count) / 100);
        if ((($count / 2) - $resul) > 0) {
            return ['first_type' => 'Regional', 'second_type' => 'Nacional',  'quantity' => ($resul)];
        }
        return ['first_type' => 'Nacional', 'second_type' => 'Regional',  'quantity' => ($count - $resul)];
    }
}
