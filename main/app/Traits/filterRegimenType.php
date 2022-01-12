<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait filterRegimenType
{
    /**
     * Build a success response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\Response
     */
    public function filterRegimenType($data)
    {
        $filtered =  $this->regimentypes->first(function ($value, $key) use ($data) {
            return  $value->name == $data;
        });
        if ($filtered) {
            return $filtered->id;
        }
        dd('Tipo de Regimen no existe');
    }
}
