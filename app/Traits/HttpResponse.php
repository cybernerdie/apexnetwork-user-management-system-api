<?php

namespace App\Traits;

trait HttpResponse
{
    /**
     * Generate a success response.
     *
     * @param  int  $code
     * @param  string  $message
     * @param  array  $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($code = 200, $message = 'success', $data = [])
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    /**
     * Generate an error response.
     *
     * @param  int  $code
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($code = 400, $message = 'Something bad happened')
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
