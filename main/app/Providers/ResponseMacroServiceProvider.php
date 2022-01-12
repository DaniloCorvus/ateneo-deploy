<?php

namespace App\Providers;

use Illuminate\Http\Response as Status;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data, $code = Status::HTTP_OK) {
            return Response::json([
                'status' => true,
                'code' => $code,
                'data' => $data,
                'err' => null
            ])->header('Content-Type', 'application/json');
        });

        Response::macro('error', function ($message, $code = 400) {
            return Response::json([
                'status' => false,
                'code' =>  $code,
                'data' =>  null,
                'err' => $message
            ])->header('Content-Type', 'application/json');
        });
    }
}
