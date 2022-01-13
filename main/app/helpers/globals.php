<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('findingKey')) {

    function findingKey($collection)
    {
        if (!isset($collection)) {
            return false;
        }

        if (!$collection) {
            return false;
        }

        return true;

        // $collection->contains(function ($value, $key) use ($search) {
        //     echo $value;
        //     echo '<br>';
        //     echo $key;
        //     echo '<br>';
        //     return $value == $search;
        // });
    }
}

if (!function_exists('getFilename')) {

    function getFilename(string $field): string
    {
        $urlBase = DB::table('site_settings')->get(['url', 'folder_functionaries'])->first();
        $filename = '_' . microtime(true) . '.' . request()->file($field)->getClientOriginalExtension();
        request()->file($field)->move(public_path() . '/' . $urlBase->folder_functionaries . '/', $filename);
        return   $urlBase->folder_functionaries . '/' . $filename;
        // return   $urlBase->url .  $urlBase->folder_functionaries . '/' . $filename;
    }
}
