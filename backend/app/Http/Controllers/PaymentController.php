<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Services\PaymeService;
use App\Services\ClickService;

class PaymentController extends Controller
{
    protected $payme;
    protected $click;

    public function __construct(PaymeService $payme, ClickService $click)
    {
        $this->payme = $payme;
        $this->click = $click;
    }

    // Create a Payme order for a booking or daily rent
    public function createPaymePayment(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|integer',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        $user = $request->user();

        $tx = Transaction::create([
            'user_id' => $user ? $user->id : null,
            'listing_id' => $request->listing_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'provider' => 'payme',
            'status' => 'pending',
        ]);

        // ask Payme service to create payment and return frontend params
        $resp = $this->payme->createPayment($tx);

        // store provider order id if returned
        if (isset($resp['order_id'])) {
            $tx->provider_order_id = $resp['order_id'];
            $tx->payload = $resp;
            $tx->save();
        }

        return response()->json(['transaction' => $tx, 'payme' => $resp]);
    }

    // Payme webhook/callback
    public function paymeCallback(Request $request)
    {
        Log::info('Payme callback', ['payload' => $request->all()]);
        try {
            $data = $request->all();
            $verified = $this->payme->verifyCallback($data, $request->header('Content-Type'));

            if (!$verified) {
                Log::warning('Payme callback signature failed');
                return response()->json(['error' => 'invalid signature'], 400);
            }

            // Example: assume data contains order_id and status
            $orderId = $data['order_id'] ?? null;
            $status = $data['status'] ?? null;

            $tx = Transaction::where('provider_order_id', $orderId)->first();
            if (!$tx) {
                return response()->json(['error' => 'tx not found'], 404);
            }

            if ($status === 'completed' || $status === 'paid') {
                $tx->status = 'paid';
                $tx->payload = $data;
                $tx->save();

                // TODO: business logic: mark booking/listing as booked etc.
            } else {
                $tx->status = 'failed';
                $tx->payload = $data;
                $tx->save();
            }

            return response()->json(['result' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Payme callback error: ' . $e->getMessage());
            return response()->json(['error' => 'server error'], 500);
        }
    }

    // Create a Click payment
    public function createClickPayment(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|integer',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        $user = $request->user();

        $tx = Transaction::create([
            'user_id' => $user ? $user->id : null,
            'listing_id' => $request->listing_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'provider' => 'click',
            'status' => 'pending',
        ]);

        $resp = $this->click->createPayment($tx);

        if (isset($resp['order_id'])) {
            $tx->provider_order_id = $resp['order_id'];
            $tx->payload = $resp;
            $tx->save();
        }

        return response()->json(['transaction' => $tx, 'click' => $resp]);
    }

    public function clickCallback(Request $request)
    {
        Log::info('Click callback', ['payload' => $request->all()]);
        try {
            $data = $request->all();
            $verified = $this->click->verifyCallback($data);
            if (!$verified) {
                Log::warning('Click callback verification failed');
                return response()->json(['error' => 'invalid signature'], 400);
            }

            $orderId = $data['order_id'] ?? null;
            $status = $data['status'] ?? null;

            $tx = Transaction::where('provider_order_id', $orderId)->first();
            if (!$tx) {
                return response()->json(['error' => 'tx not found'], 404);
            }

            if ($status === 'completed' || $status === 'paid') {
                $tx->status = 'paid';
                $tx->payload = $data;
                $tx->save();
                // TODO: business logic
            } else {
                $tx->status = 'failed';
                $tx->payload = $data;
                $tx->save();
            }

            return response()->json(['result' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Click callback error: ' . $e->getMessage());
            return response()->json(['error' => 'server error'], 500);
        }
    }
}
