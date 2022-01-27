<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTurnoRotativo extends Model
{

    protected $table = 'horario_turno_rotativo';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function turnoRotativo()
    {
        return $this->belongsTo(RotatingTurn::class, 'turno_rotativo_id');
    }
}
