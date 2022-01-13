<?php

namespace App\CustomFacades;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImgUpload
{
    public static function converFromBase64($image)
    {
        $image =   preg_replace("#^data:image/\w+;base64,#i", "", $image);
        $image = str_replace(' ', '+', $image);
        $image = base64_decode($image);
        $file_path = 'people/' . Str::random(30) . time() . '.png';
        self::upload($file_path, $image);
        return ['image_blob' => $file_path, 'image' => self::upload($file_path, $image)];
    }

    public static function upload($file_path, $image)
    {
        Storage::disk('public')->put($file_path, $image, 'public');
        return  Storage::disk('public')->url($file_path);
    }

    public static function validate($img)
    {
        if ($img && $img != null  && $img != 'null' && preg_match("#^data:image/\w+;base64,#i", $img)) {
            return true;
        }
        return false;
    }

    public static function deleteImg($img): void
    {
        // File::exists(public_path() . '/app/public/' . $img);
        Storage::disk('public')->delete($img);
    }
}
