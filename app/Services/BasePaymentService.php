<?php

namespace App\Services;

abstract class BasePaymentService{
    protected function log($message)
    {
        \Log::info('[PAYMENT] ' . $message);
    }
}
