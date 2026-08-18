<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickService
{
    protected $merchant_id;
    protected $secret;
    protected $service_id;
    protected $base;

    public function __construct()
    {
        $this->merchant_id = env('CLICK_MERCHANT_ID');
        $this->service_id = env('CLICK_SERVICE_ID');
        $this->secret = env('CLICK_SECRET');
        $this->base = env('CLICK_API_URL', 'https://api.click.uz');
    }

    /**
     * Create a Click payment (prepare). Returns array with order_id and checkout URL.
     * This method will attempt a real API call when CLICK_API_URL is configured; otherwise returns a simulated response.
     */
    public function createPayment(Transaction $tx)
    {
        $merchantTransId = (string)$tx->id;
        $amount = (int)round($tx->amount); // Click usually expects integer UZS (no subunits)
        $action = 0; // prepare
        $signTime = gmdate('Y-m-d H:i:s');

        // sign_string for prepare (best-effort): md5(service_id + merchant_trans_id + secret + amount + action + sign_time)
        $signString = $this->service_id . $merchantTransId . $this->secret . $amount . $action . $signTime;
        $sign = md5($signString);

        $payload = [
            'service_id' => (int)$this->service_id,
            'merchant_trans_id' => $merchantTransId,
            'amount' => $amount,
            'action' => $action,
            'sign_time' => $signTime,
            'sign_string' => $sign,
        ];

        if ($this->base) {
            try {
                $url = rtrim($this->base, '/') . '/payment/prepare/';
                $resp = Http::timeout(10)->post($url, $payload);
                if ($resp->successful()) {
                    $data = $resp->json();
                    return [
                        'order_id' => $data['merchant_prepare_id'] ?? ('click_' . $merchantTransId),
                        'checkout_url' => $data['payment_url'] ?? ($this->base . '/pay/' . $merchantTransId),
                        'payload' => $data,
                    ];
                }

                Log::warning('Click API non-success: ' . $resp->body());
            } catch (\Exception $e) {
                Log::error('Click API call failed: ' . $e->getMessage());
            }
        }

        // Fallback simulated response
        return [
            'order_id' => 'click_' . $merchantTransId,
            'checkout_url' => ($this->base ?: 'https://my.click.uz') . '/pay/mock/' . $merchantTransId,
            'payload' => $payload,
        ];
    }

    /**
     * Verify Click callback. Expects click_trans_id, service_id, merchant_trans_id, amount, action, sign_time and sign_string
     */
    public function verifyCallback(array $data): bool
    {
        if (!isset($data['click_trans_id'], $data['service_id'], $data['merchant_trans_id'], $data['amount'], $data['action'], $data['sign_time'], $data['sign_string'])) {
            Log::warning('Click callback missing fields: ' . json_encode($data));
            return false;
        }

        $expected = md5(
            $data['click_trans_id'] .
            $data['service_id'] .
            $this->secret .
            $data['merchant_trans_id'] .
            $data['amount'] .
            $data['action'] .
            $data['sign_time']
        );

        return hash_equals($expected, $data['sign_string']);
    }
}
