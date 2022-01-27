<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $fillable = [
        "name",
        "number",
        "department_id",
        "company_id",
        "regimen",
        "site",
        "eps_id"
    ];
}
