<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;
    //
    public function store(OrderPostRequest $request): \Illuminate\Http\JsonResponse{

        $request = $request->validated();

        try {
            $order = Order::create($request);
            return $this->successResponse($order,'Order Created Successfully');
        }catch (\Exception $exception){
            return $this->exceptionResponse($exception->getMessage());
        }

    }

    public function show(Order $order): \Illuminate\Http\JsonResponse{
        return $this->successResponse($order,'Fetch Order Successfully');
    }
}
