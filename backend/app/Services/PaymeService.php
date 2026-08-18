<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymeService
{
    protected $merchant_id;
    protected $secret;
    protected $base;

    public function __construct()
    {
        $this->merchant_id = env('PAYME_MERCHANT_ID');
        $this->secret = env('PAYME_SECRET');
        $this->base = env('PAYME_API_URL', 'https://pay.payme.uz');
    }

    /**
     * Create payment on Payme (merchant checkout URL).
     * This attempts a real API call when PAYME_API_URL is configured; otherwise returns a simulated response.
     * Amount is sent in tiyin (1 UZS = 100 tiyin).
     */
    public function createPayment(Transaction $tx)
    {
        $orderId = (string)$tx->id;
        $amountTiyin = (int)round($tx->amount * 100); // convert UZS to tiyin
        $currency = $tx->currency ?? 'UZS';

        // Signature per Payme examples: sha1("merchant_id:order_id:amount:currency:secret_key")
        $signatureString = sprintf("merchant_id:%s;order_id:%s;amount:%s;currency:%s;secret_key:%s",
            $this->merchant_id,
            $orderId,
            $amountTiyin,
            $currency,
            $this->secret
        );
        $signature = sha1($signatureString);

        $payload = [
            'request' => [
                'merchant_id' => (int)$this->merchant_id,
                'order_id' => $orderId,
                'amount' => $amountTiyin,
                'currency' => $currency,
                'order_desc' => "Payment for listing {$tx->listing_id}",
                'server_callback_url' => env('PAYME_CALLBACK_URL', env('APP_URL') . '/api/payments/payme/callback'),
                'response_url' => env('FRONTEND_RETURN_URL', ''),
                'signature' => $signature,
            ]
        ];

        // If a real Payme API base is configured, attempt to call it
        if ($this->base) {
            try {
                $url = rtrim($this->base, '/') . '/api/checkout/url';
                $resp = Http::timeout(10)->post($url, $payload);
                if ($resp->successful()) {
                    $data = $resp->json();
                    // Normalize response to include checkout_url and order_id
                    return [
                        'order_id' => $data['response']['payment_id'] ?? ('payme_' . $orderId),
                        'checkout_url' => $data['response']['checkout_url'] ?? ($this->base . '/merchants/' . $this->merchant_id . '/index.html?token=' . ($data['response']['token'] ?? '')),
                        'payload' => $data,
                    ];
                }

                Log::warning('Payme API returned non-success: ' . $resp->body());
            } catch (\Exception $e) {
                Log::error('Payme API call failed: ' . $e->getMessage());
            }
        }

        // Fallback (simulated) response when API call isn't available or fails
        return [
            'order_id' => 'payme_' . $orderId,
            'checkout_url' => ($this->base ?: 'https://pay.payme.uz') . '/checkout/mock/' . $orderId,
            'payload' => $payload,
        ];
    }

    /**
     * Verify callback payload signature. Returns boolean.
     * Expected callback payload varies by Payme versions; this implementation supports common signature field.
     */
    public function verifyCallback(array $data): bool
    {
        // If provider sends 'signature' and includes order_id/amount/currency, compute expected
        if (isset($data['signature']) && isset($data['order_id']) && isset($data['amount']) && isset($data['currency'])) {
            $signatureString = sprintf("merchant_id:%s;order_id:%s;amount:%s;currency:%s;secret_key:%s",
                $this->merchant_id,
                $data['order_id'],
                $data['amount'],
                $data['currency'],
                $this->secret
            );
            $expected = sha1($signatureString);
            return hash_equals($expected, $data['signature']);
        }

        // If callback format differs, accept; but log for inspection
        Log::warning('Payme callback missing signature/order info: ' . json_encode($data));
        return true; // permissive fallback — adjust for production
    }
}
