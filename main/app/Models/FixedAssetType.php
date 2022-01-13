<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAssetType extends Model
{

    protected $fillable = [
        'name',
        'category',
        'useful_life_niif',
        'annual_depreciation_percentage_niif',
        'uselful_life_pcga',
        'annual_depreciation_percentage_pcga',
        'niif_depreciation_account_plan_id',
        'pcga_depreciation_account_plan_id',
        'niif_account_plan_id',
        'pcga_account_plan_id',
        'niif_account_plan_credit_depreciation_id',
        'pcga_account_plan_credit_depreciation_id',
        'pcga_account_plan_debit_depreciation_id',
        'niif_account_plan_debit_depreciation_id',
        'consecutive',
        'useful_life_pcga',
        'mantis',
        'state'
    ];


}
