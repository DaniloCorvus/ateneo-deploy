<?php

namespace App;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RCuentaMedica extends Model
{
    public function tercero()
    {
        // if ($this->type == 1) {
            return $this->belongsTo(Company::class, 'third_part_id', 'tin');
        // }
        // if ($this->type == 2) {
            // return $this->belongsTo(Company::class, 'third_part_id');
        // }
    }
}
