<?php

namespace App\Providers;

use App\CustomFacades\ImgUpload;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FacadeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        App::bind('img-upload', function () {
            return new ImgUpload();
        });
    }
}
