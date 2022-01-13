<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('saveBase64')) {
    function saveBase64($image, $path, $public = true)
    {
        $image = base64_decode(
            preg_replace(
                "#^data:image/\w+;base64,#i",
                "",
                $image
            )
        );

        $file_path = $path . Str::random(30) . time() . ".png";
        if ($public) {
            Storage::disk('public')->put($file_path, $image, "public");
            return $file_path;
        }
        Storage::put($file_path, $image, "public");

        return $file_path;
    }
}


if (!function_exists('saveBase64File')) {
    function saveBase64File($file, $path, $public = true, $type = ".pdf")
    {
        $file = base64_decode(
            preg_replace(
                "#^data:application/\w+;base64,#i",
                "",
                $file
            )
        );
        $file_path = $path . Str::random(30) . time() . $type;
        if ($public) {
            Storage::disk('public')->put($file_path, $file, "public");
            return $file_path;
        } 
            Storage::put($file_path, $file, "public");
            return $file_path;
    }
}


