<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $with = ['questions'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
