<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioTurnoFijo extends Model
{
    protected $table = 'fixed_turn_diaries';
    protected $guarded = ['id'];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function turnoFijo()
    {
        return $this->belongsTo(FixedTurn::class);
    }
}
