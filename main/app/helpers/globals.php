<?php

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
