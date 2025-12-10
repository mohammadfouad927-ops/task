<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    //
    protected function successResponse($data = null, string $message = 'success', int $code = 200): JsonResponse {
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function failureResponse($errors = null, string $message = 'failure', int $code = 400): JsonResponse {
        return response()->json([
            'status' => false,
            'errors' => $errors,
            'message' => $message
        ], $code);
    }

    protected  function exceptionResponse( string $message = 'exception', int $code = 500): JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }
}
