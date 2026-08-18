<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class ClickService
{
    protected $merchant_id;
    protected $secret;
    protected $base;

    public function __construct()
    {
        $this->merchant_id = env('CLICK_MERCHANT_ID');
        $this->secret = env('CLICK_SECRET');
        $this->base = env('CLICK_BASE_URL', 'https://my.click.uz'); // placeholder
    }

    public function createPayment(Transaction $tx)
    {
        // Build a payload that Click requires (merchant parameters, amount, order id)
        $payload = [
            'merchant_id' => $this->merchant_id,
            'amount' => (int)($tx->amount * 100),
            'currency' => $tx->currency,
            'description' => "Payment for listing {$tx->listing_id}",
            'order_id' => $tx->id,
            'return_url' => env('FRONTEND_RETURN_URL', ''),
        ];

        // Real integration: call Click create order API and return response.
        return [
            'order_id' => 'click_' . $tx->id,
            'checkout_url' => $this->base . '/pay/' . $tx->id,
            'payload' => $payload,
        ];
    }

    public function verifyCallback(array $data)
    {
        // Implement signature check per Click docs. Placeholder true for now.
        return true;
    }
}
