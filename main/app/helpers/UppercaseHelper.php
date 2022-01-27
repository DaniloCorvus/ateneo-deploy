<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!function_exists('toUpper')) {
    function toUpper($array = [])
    {
        return   Collect($array)->map(function ($item) {
            return Str::upper($item);
        })->all();
    }
}
