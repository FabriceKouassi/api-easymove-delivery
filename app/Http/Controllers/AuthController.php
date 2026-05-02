<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\OtpSendRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\OtpCheckRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Symfony\Component\Clock\now;

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

        if($data['role'] === RoleEnum::CONDUCTEUR->value)
        {
            if ($request->hasFile('img') === false) {
                throw new HttpException(403, "Veuillez ajouter votre image");
            }

            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $userFolder = $data['phone'] . '-' . $data['name'];

                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                $path = Storage::disk('public')->putFileAs(
                    'users/' . $userFolder,
                    $file,
                    $filename
                );

                $data['img'] = $path;
            }
        }

        if($data['role'] !== RoleEnum::CONDUCTEUR->value)
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
            'message' => 'OTP envoye avec succes',
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

        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new HttpException(403, "Identifiants incorrects.");
        }

        // Générer un token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function adminLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }
}
