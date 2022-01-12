<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AgendmientoResource extends JsonResource
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
            'title'  => $this->type_agenda_id,
            'start' => $this->date_start,
            'end' => $this->date_end,
            'className' => "bg-warning text-white",
        ];
    }
}
