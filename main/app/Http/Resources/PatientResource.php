<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'Tipo_Identificacion' => strtoupper($this->typedocument->code),
            'Identificacion' => strtoupper ($this->identifier),
            'Regimen' => strtoupper ($this->regimentype->name),
            'Apellidos' => strtoupper ($this->surname . ' ' .  $this->secondsurname),
            'Nombres' => strtoupper ($this->firstname . ' ' . $this->middlename),
            'EPS' => strtoupper ($this->eps->name),
            'IPS' => strtoupper ($this->company->name),
            'Regional' => strtoupper ($this->regional->id),
            'Departamento' => strtoupper ($this->department->name),
            'Ciudad' => strtoupper ($this->municipality->name),
            'Sede' => strtoupper ($this->location->name),
            'Nivel' => strtoupper ($this->level->name),
            'Direccion' => strtoupper ($this->address),
            'Telefono' => strtoupper ($this->phone),
            'Correo' => strtoupper ($this->email),
            'Estado' => $this->state
        ];
    }
}
