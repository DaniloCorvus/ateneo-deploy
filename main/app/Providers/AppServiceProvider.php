<?php

namespace App\Providers;

use App\Builder\RelationsBuilder;
use App\Mixins\CollectionMixin;
use App\Mixins\ResponseMixin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::mixin(new ResponseMixin);
        Collection::mixin(new CollectionMixin);
    }
}
