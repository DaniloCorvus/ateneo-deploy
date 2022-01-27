<?php

if (!function_exists('customSnakeCase')) {

    function customSnakeCase($string)
    {

        if (preg_match('/[A-Z]/', $string) === 0) {
            return $string;
        }
        $pattern = '/([a-z])([A-Z])/';
        $r = strtolower(preg_replace_callback($pattern, function ($a) {
            return $a[1] . "_" . strtolower($a[2]);
        }, $string));

        return $r;
    }
}
