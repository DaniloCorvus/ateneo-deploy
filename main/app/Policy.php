<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        "contract_id",
        "code",
        "start",
        "end",
        "name",
        "coverage"
    ];
}
