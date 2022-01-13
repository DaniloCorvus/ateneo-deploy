<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    //

    protected $fillable = ['name'];

    /**
     * Un grupo tiene muchas dependencias
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dependencies()
    {
        return $this->hasMany(Dependency::class);
    }
}
