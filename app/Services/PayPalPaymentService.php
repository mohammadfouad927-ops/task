<?php

namespace App\Services;

use App\Interfaces\PaymentInterface;
use App\Models\Order;
use App\Models\Payment;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment as PayPalPayment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;



class PayPalPaymentService  implements PaymentInterface
{
    protected $paypal;
    public function __construct()
    {
        $this->paypal = new ApiContext(
            new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret')
            )
        );

        $this->paypal->setConfig(config('paypal.settings'));
    }



    public function initiatePayment(Order $order): Payment
    {

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');


        $amountObj = new Amount();
        $amountObj->setTotal($order->amount);
        $amountObj->setCurrency($order->currency);


        $transaction = new Transaction();
        $transaction->setAmount($amountObj)
            ->setDescription("Payment for order #{$order->id}");


        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(url('/api/payments/success'))
            ->setCancelUrl(url('/api/payments/cancel'));


        $paypalPayment = new PayPalPayment();
        $paypalPayment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        $paypalPayment->create($this->paypal);


        return Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'paypal',
            'transaction_id' => $paypalPayment->getId(),
            'amount' => $order->amount,
            'status' => 'pending',
            'metadata' => [
                'approval_link' => $paypalPayment->getApprovalLink()
            ]
        ]);
    }

    public function executePayPalPayment(string $paymentId, string $payerId): Payment
    {
        $payment = PayPalPayment::get($paymentId, $this->paypal);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $result = $payment->execute($execution, $this->paypal);


        $record = Payment::where('transaction_id', $paymentId)->firstOrFail();
        $record->status = 'completed'; // mark as completed
        $record->metadata = array_merge($record->metadata ?? [], [
            'paypal_result' => $result->toArray()
        ]);
        $record->save();

        Order::find($record->order_id)->update(['stauts' => 'completed']);

        return $record;
    }

    public function cancelPayment(string $paymentId): Payment
    {
        $record = Payment::where('transaction_id', $paymentId)->firstOrFail();
        $record->status = 'cancelled';
        $record->save();

        Order::find($record->order_id)->update(['stauts' => 'cancelled']);

        return $record;
    }

}
