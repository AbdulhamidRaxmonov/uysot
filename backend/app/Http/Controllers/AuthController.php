<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\EskizService;

class AuthController extends Controller
{
    protected $eskiz;

    public function __construct(EskizService $eskiz)
    {
        $this->eskiz = $eskiz;
    }

    // Send verification code to phone (create user if not exists)
    public function sendCode(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $phone = $request->input('phone');

        // Normalize phone if needed (basic)
        $normPhone = preg_replace('/[^0-9+]/', '', $phone);

        $user = User::firstOrCreate(['phone' => $normPhone]);

        // generate 4-6 digit code
        $code = rand(1000, 9999);

        // cache code for 5 minutes
        $key = "phone_verif:{$normPhone}";
        Cache::put($key, ['code' => (string)$code, 'user_id' => $user->id], now()->addMinutes(5));

        // send SMS via EskizService
        $message = "Sizning tasdiqlash kodingiz: {$code}";
        try {
            $this->eskiz->sendSms($normPhone, $message);
        } catch (\Exception $e) {
            Log::error('Eskiz SMS failed: ' . $e->getMessage());
            // do not fail silently for now, inform client
            return response()->json(['message' => 'SMS yuborilmadi, keyinroq urinib ko\'ring'], 500);
        }

        return response()->json(['message' => 'Kod yuborildi']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['phone' => 'required|string', 'code' => 'required|string']);
        $phone = preg_replace('/[^0-9+]/', '', $request->input('phone'));
        $code = $request->input('code');

        $key = "phone_verif:{$phone}";
        $data = Cache::get($key);
        if (!$data || $data['code'] !== $code) {
            return response()->json(['message' => 'Kod noto\'g\'ri yoki muddati o\'tgan'], 422);
        }

        $user = User::find($data['user_id']);
        if (!$user) {
            return response()->json(['message' => 'Foydalanuvchi topilmadi'], 404);
        }

        // authentication: create token
        $token = $user->createToken('uysot_token')->plainTextToken;

        // forget the code
        Cache::forget($key);

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
