<?php

namespace App\CustomFacades;

use Illuminate\Support\Facades\Facade;

class ImgUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'img-upload';
    }
}
