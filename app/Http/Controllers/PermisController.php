<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\CreatePermisRequest;
use App\Models\Permis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermisController extends Controller
{
    public function create(CreatePermisRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->where('phone', $data['user_phone'])->first();

        $data['user_id'] = $user->id;

        if (is_null($user)) {
            return response()->json([
                'message' => 'Utilisateur introuvable'
            ]);
        }
        if ($user->role !== RoleEnum::CONDUCTEUR->value) {
            throw new HttpException(403, "Action non autorisée");
        }

        if ($request->hasFile('front_img')) {
            $file = $request->file('front_img');
            $folder = 'users/' . $user->phone . '-' . $user->name . '/permis';

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('public')->putFileAs(
                $folder,
                $file,
                $filename
            );

            $data['front_img'] = $path;
        }

        if ($request->hasFile('back_img')) {
            $file = $request->file('back_img');
            $folder = 'users/' . $user->phone . '-' . $user->name . '/permis';

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('public')->putFileAs(
                $folder,
                $file,
                $filename
            );

            $data['back_img'] = $path;
        }

        if ($request->hasFile('human_selfie_img')) {
            $file = $request->file('human_selfie_img');
            $folder = 'users/' . $user->phone . '-' . $user->name . '/permis';

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('public')->putFileAs(
                $folder,
                $file,
                $filename
            );

            $data['human_selfie_img'] = $path;
        }

        $permis = Permis::query()->create($data);

        return response()->json([
            'message' => 'Permis de conduire enregistré',
            'permis' => $permis->load('user')
        ]);
    }
}
