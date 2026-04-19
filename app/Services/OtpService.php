<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function generate(string $phone): ?string
    {
        $otp = Otp::query()->where('phone', $phone)->latest()->first();

        // 1. Si OTP encore valide → PAS de SMS
        if ($otp && now()->lt($otp->expires_at)) {
            return null;
        }

        // 2. Cooldown (anti spam discret)
        if ($otp && now()->diffInSeconds($otp->created_at) < 60) {
            throw ValidationException::withMessages([
                'phone' => 'Veuillez patienter avant de redemander un code'
            ]);
        }

        // 3. Limite soft (coût maîtrisé sans frustrer)
        $count = Otp::where('phone', $phone)
            ->whereDate('created_at', today())
            ->count();

        if ($count >= 5) {
            throw ValidationException::withMessages([
                'phone' => 'Trop de demandes aujourd’hui'
            ]);
        }

        Otp::query()->where('phone', $phone)->delete();

        $code = random_int(10000, 99999);

        Otp::query()->create([
            'phone' => $phone,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);

        //send otp

        return $code;
    }

    public function check(string $phone, string $code): bool
    {
        $otp = Otp::query()->where('phone', $phone)->latest()->first();

        if (!$otp) return false;

        if (now()->greaterThan($otp->expires_at)) return false;

        if ($otp->attempts >= 5) return false;

        if (! Hash::check($code, $otp->code)) {
            $otp->increment('attempts');
            return false;
        }

        $otp->delete();

        return true;
    }
}
