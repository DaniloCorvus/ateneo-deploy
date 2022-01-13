<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
    /**
     * Build a success response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\Response
     */
    public function success($data, $code = Response::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'data' => $data,
            'err' => null
        ])->header('Content-Type', 'application/json');
    }

    /**
     * Build a valid response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function validResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json(['data' => $data], $code);
    }

    /**
     * Build error responses
     * @param  string $message
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function error($message, $code)
    {
        return response()->json([
            'status' => false,
            'code' =>  $code,
            'data' =>  null,
            'err' => $message
        ])->header('Content-Type', 'application/json');
    }

    public function errorResponse($message, $code = 400)
    {
        return response()->json($message, $code)->header('Content-Type', 'application/json');
    }

    /**
     * Return an error in JSON format
     * @param  string $message
     * @param  int $code
     * @return Illuminate\Http\Response
     */
    public function errorMessage($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }
}
