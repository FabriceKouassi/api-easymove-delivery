<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\Auth\OtpSendRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\OtpCheckRequest;
use App\Models\User;
use App\Services\OtpService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    protected $otpService;
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if($data['role'] === RoleEnum::CLIENT->value || $data['role'] === RoleEnum::ADMIN->value)
        {
            $data['isValidated'] = true;
        }

        $user = User::query()->create($data);

        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function sendOtp(OtpSendRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('phone', $data['phone'])->first();

        if ($user->isValidated === false)
        {
            throw new HttpException(403, "Merci d'attendre la validation de votre compte.");
        }

        $code = $this->otpService->generate($data['phone']);

        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => 'OTP envoyé avec succès',
        ]);
    }

    public function checkOtpLogin(OtpCheckRequest $request)
    {
        $data = $request->validated();

        $valid = $this->otpService->check($data['phone'], $data['code']);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Code invalide ou expiré',
            ], 422);
        }

        $user = User::query()->where("phone", $data['phone'])->first();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ]);
    }
}
