<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiateRequest;
use App\Http\Requests\PaymentCancelRequest;
use App\Http\Requests\PaymentSuccessRequest;
use App\Interfaces\PaymentInterface;
use App\Models\Order;
use App\Traits\ApiResponse;
use App\Services\PayPalPaymentService;

class PaymentController extends Controller
{
    use ApiResponse;
    protected PaymentInterface $paymentService;

    public function __construct(PayPalPaymentService $paymentService){
        $this->paymentService = $paymentService;
    }

    public function initiate(InitiateRequest $request)
    {
        $data = $request->validated();

        $order = Order::find($data['orderId']);

        try {
            $payment = $this->paymentService->initiatePayPalPayment($order);

            return $this->successResponse([
                'payment_id' => $payment->id,
                'payment_url' => $payment->metadata['approval_link'],
                'status' => $payment->status
            ]);

        } catch (\Exception $e) {
            return $this->exceptionResponse( $e->getMessage(), 500);
        }
    }

    public function success(PaymentSuccessRequest $request)
    {
        $data = $request->validated();

        try {
            $payment = $this->paymentService->executePayPalPayment(
                $data->paymentId,
                $data->PayerID
            );

            return $this->successResponse([
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'message' => 'Payment completed successfully'
            ]);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e->getMessage(), 500);
        }
    }

    public function cancel(PaymentCancelRequest $request)
    {
        $data = $request->validated();
        try {
            $payment = $this->paymentService->cancelPayment($data->paymentId);

            return $this->successResponse([
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'message' => 'Payment was cancelled'
            ]);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e->getMessage(), 500);
        }
    }

}
