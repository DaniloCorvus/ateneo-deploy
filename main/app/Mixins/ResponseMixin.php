<?php

namespace App\Mixins;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as Status;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class ResponseMixin
{
    /**
     * @return \Closure
     */
    public function error()
    {
        Response::macro('error', function ($message, $code = 400) {
            return Response::json([
                'status' => false,
                'code' =>  $code,
                'data' =>  null,
                'err' => $message
            ])->header('Content-Type', 'application/json');
        });
    }

    /**
     * @return \Closure
     */
    public function success()
    {
        Response::macro('success', function ($data, $code = Status::HTTP_OK) {
            return Response::json([
                'status' => true,
                'code' => $code,
                'data' => $data,
                'err' => null
            ])->header('Content-Type', 'application/json');
        });
    }
}
