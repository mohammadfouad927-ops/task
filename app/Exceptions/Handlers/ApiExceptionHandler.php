<?php

namespace App\Exceptions\Handlers;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionHandler extends Exception
{
    use ApiResponse {
        exceptionResponse as protected traitExceptionResponse;
    }

    public static function register($exceptions){

        $exceptions->render(function (NotFoundHttpException  $e, $request ) {
            if($request->expectsJson()){
                return response()->json([
                        'status' => false,
                        'message' => 'Not Found'
                    ]
                ,404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if(request()->expectsJson()){
                if($request->expectsJson()){
                    return response()->json([
                            'status' => false,
                            'message' => 'Not Found'
                        ]
                        ,404);
                }
            }
        });

        }
}
