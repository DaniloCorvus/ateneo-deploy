<?php

namespace App;
use App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class HistoryAgendamiento extends Model
{
    protected $fillable = [
        'appointment_id',
        'user_id',
        'description',
        'agendamiento_id',
        'icon'
        ];
        
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
