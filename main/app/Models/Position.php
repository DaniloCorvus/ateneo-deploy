<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /**Cargos */
    protected $fillable = ['name', 'dependency_id'];

    //

    /**
     * una posicion pertenece a una dependencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Una posicion tiene muchos contratos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function worContract()
    {
        return $this->hasMany(WorkContract::class);
    }
}
