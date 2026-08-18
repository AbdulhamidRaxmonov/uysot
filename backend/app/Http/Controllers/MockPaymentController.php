<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Services\PaymeService;
use App\Services\ClickService;

class MockPaymentController extends Controller
{
    protected $payme;
    protected $click;

    public function __construct(PaymeService $payme, ClickService $click)
    {
        $this->payme = $payme;
        $this->click = $click;
    }

    // Simulate a Payme callback for transaction id {id}
    public function simulatePaymeCallback(Request $request, $id)
    {
        $tx = Transaction::find($id);
        if (!$tx) return response()->json(['error' => 'transaction not found'], 404);

        // Build a callback payload similar to Payme
        $payload = [
            'order_id' => $tx->provider_order_id ?: ('payme_' . $tx->id),
            'amount' => (int)round($tx->amount * 100),
            'currency' => $tx->currency ?? 'UZS',
            'order_status' => 'paid',
            'payment_id' => 'sim_payme_' . $tx->id,
        ];

        // compute signature using service method expectation
        $payload['signature'] = sha1(sprintf("merchant_id:%s;order_id:%s;amount:%s;currency:%s;secret_key:%s",
            env('PAYME_MERCHANT_ID'),
            $payload['order_id'],
            $payload['amount'],
            $payload['currency'],
            env('PAYME_SECRET')
        ));

        // Call PaymentController logic via service verification to keep behavior same.
        $verified = $this->payme->verifyCallback($payload);
        if (!$verified) {
            return response()->json(['error' => 'signature invalid'], 400);
        }

        $tx->status = 'paid';
        $tx->payload = $payload;
        $tx->save();

        Log::info('Mock Payme callback simulated for tx ' . $id);
        return response()->json(['result' => 'ok', 'transaction' => $tx]);
    }

    // Simulate a Click callback for transaction id {id}
    public function simulateClickCallback(Request $request, $id)
    {
        $tx = Transaction::find($id);
        if (!$tx) return response()->json(['error' => 'transaction not found'], 404);

        $click_trans_id = 'sim_click_' . $tx->id;
        $service_id = env('CLICK_SERVICE_ID');
        $merchant_trans_id = $tx->id;
        $amount = (int)round($tx->amount);
        $action = 1; // completed
        $sign_time = gmdate('Y-m-d H:i:s');

        $sign_string = md5($click_trans_id . $service_id . env('CLICK_SECRET') . $merchant_trans_id . $amount . $action . $sign_time);

        $payload = [
            'click_trans_id' => $click_trans_id,
            'service_id' => $service_id,
            'merchant_trans_id' => $merchant_trans_id,
            'amount' => $amount,
            'action' => $action,
            'sign_time' => $sign_time,
            'sign_string' => $sign_string,
            'status' => 'completed',
        ];

        $verified = $this->click->verifyCallback($payload);
        if (!$verified) {
            return response()->json(['error' => 'signature invalid'], 400);
        }

        $tx->status = 'paid';
        $tx->payload = $payload;
        $tx->save();

        Log::info('Mock Click callback simulated for tx ' . $id);
        return response()->json(['result' => 'ok', 'transaction' => $tx]);
    }
}
