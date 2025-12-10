<?php

namespace App\Interfaces;

use App\Models\Order;
use App\Models\Payment;
interface PaymentInterface
{
    public function initiatePayment(Order $order): Payment;
}
