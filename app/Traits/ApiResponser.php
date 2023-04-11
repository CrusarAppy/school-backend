<?php


namespace App\Traits;


trait ApiResponser
{
    public function successResponse($message=null, $data = null, $statusCode = 200)
    {
        return response()->json([
            'message'   => $message,
            'status'     => 'success',
            'data'      => $data,
        ], $statusCode);
    }

    public function errorResponse($message, $statusCode = 500)
    {
        return response()->json([
            'message'   => $message,
            'status'     => 'error'
        ], $statusCode);
    }
}
