<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\CreateVehiculeRequest;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VehiculeController extends Controller
{
    public function create(CreateVehiculeRequest $request)
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

        if ($request->hasFile('car_img')) {
            $file = $request->file('car_img');
            $folder = 'users/' . $user->phone . '-' . $user->name . '/vehicule';

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('public')->putFileAs(
                $folder,
                $file,
                $filename
            );

            $data['car_img'] = $path;
        }

        if ($request->hasFile('carte_grise_img')) {
            $file = $request->file('carte_grise_img');
            $folder = 'users/' . $user->phone . '-' . $user->name . '/vehicule';

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('public')->putFileAs(
                $folder,
                $file,
                $filename
            );

            $data['carte_grise_img'] = $path;
        }

        $vehicule = Vehicule::query()->create($data);

        return response()->json([
            'message' => 'Vehicule enregistré',
            'vehicule' => $vehicule->load('user')
        ]);
    }
}
