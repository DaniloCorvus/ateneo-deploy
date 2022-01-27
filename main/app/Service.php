<?php

namespace App;

use App\Models\Cup;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        "technic_note_id",
        "cup_id",
        "code",
        "value",
        "frequency",
        "centro_costo_id"
    ];

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class);
    }
    public function cup(): BelongsTo
    {
        return $this->belongsTo(Cup::class);
    }
}
