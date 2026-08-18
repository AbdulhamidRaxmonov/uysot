<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class PaymeService
{
    protected $merchant_id;
    protected $secret;
    protected $base;

    public function __construct()
    {
        $this->merchant_id = env('PAYME_MERCHANT_ID');
        $this->secret = env('PAYME_SECRET');
        $this->base = env('PAYME_BASE_URL', 'https://checkout.paycom.uz'); // placeholder
    }

    // Create payment on Payme side (this is a sketch; adapt to Payme API docs)
    public function createPayment(Transaction $tx)
    {
        // Typical approach: build data and sign it or call merchant API
        $payload = [
            'merchant_id' => $this->merchant_id,
            'amount' => (int)($tx->amount * 100), // in cents/tiyin depending on provider
            'currency' => $tx->currency,
            'description' => "Payment for listing {$tx->listing_id}",
            'order_id' => $tx->id,
            'return_url' => env('FRONTEND_RETURN_URL', ''),
        ];

        // For real integration, send request to Payme merchant create endpoint and return response.
        // Here we return a skeleton response for mobile to use.
        return [
            'order_id' => 'payme_' . $tx->id,
            'checkout_url' => $this->base . '/pay/' . $tx->id,
            'payload' => $payload,
        ];
    }

    // Verify callback: signature etc (implementation depends on Payme callback format)
    public function verifyCallback(array $data, $contentType = null)
    {
        // Implement signature verification as per Payme docs.
        // Placeholder returns true for now.
        return true;
    }
}
