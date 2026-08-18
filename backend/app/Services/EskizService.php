<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EskizService
{
    protected $base = 'https://notify.eskiz.uz';

    public function sendSms(string $phone, string $message)
    {
        $token = env('ESKIZ_API_KEY');
        if (!$token) {
            throw new \Exception('ESKIZ_API_KEY not set');
        }

        $url = $this->base . '/api/message/sms/send';

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post($url, [
            'mobile_phone' => $phone,
            'message' => $message,
        ]);

        if ($resp->failed()) {
            throw new \Exception('Eskiz API error: ' . $resp->body());
        }

        return $resp->json();
    }
}
