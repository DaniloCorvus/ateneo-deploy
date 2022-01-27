<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function usuario()
    {
       return $this->BelongsToMany(Usuario::class);
    }
}
