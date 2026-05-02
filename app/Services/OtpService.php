<?php

namespace App\Services;

use App\Models\Otp;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twilio\Rest\Client;

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
        // if ($otp && now()->diffInSeconds($otp->created_at) < 60) {
        //     throw new HttpException(403, "Veuillez patienter avant de redemander un code");
        // }

        // 3. Limite soft (coût maîtrisé sans frustrer)
        $count = Otp::where('phone', $phone)
            ->whereDate('created_at', today())
            ->count();

        if ($count >= 5) {
            throw new HttpException(403, "Trop de demandes aujourd'hui");
        }

        Otp::query()->where('phone', $phone)->delete();

        $code = random_int(10000, 99999);

        Otp::query()->create([
            'phone' => $phone,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);


        //send otp //+18777804236

        $id = config('services.twilio.sid');
        $token = config('services.twilio.token');

        // $twilio = new Client($id, $token);
        // try {
        //     $twilio->messages->create(
        //         $phone,
        //         [
        //             "body" => "Votre OTP est: $code",
        //             "from" => config("services.twilio.from"),
        //         ]
        //     );
        // } catch (Exception $e) {
        //     Log::error($e->getMessage());
        //     throw new HttpException(403, "Erreur lors de l'envoi du SMS");
        // }

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
