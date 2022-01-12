<?php

namespace App;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{

    public function scopeFindCustom($query, $id)
    {
        return $query->select(['name', 'id'])->firstWhere('id', $id);
    }

    public function usuario()
    {
        return $this->BelongsToMany(Usuario::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
