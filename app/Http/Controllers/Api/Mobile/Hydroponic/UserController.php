<?php

namespace App\Http\Controllers\Api\Mobile\Hydroponic;

use App\Http\Controllers\Controller;
use App\Models\HydroponicUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected string $filePath = 'bitanic-photo/hydroponic/user/';

    public function show() : JsonResponse {
        return response()
            ->json($this->hydroponicGuard()->user());
    }

    public function updateProfile(Request $request) : JsonResponse
    {
        $hydroponicUser = HydroponicUser::find($this->hydroponicGuard()->id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hydroponic_users,email,' . $hydroponicUser->id,
            'username' => 'required|string|max:255|alpha_dash|unique:hydroponic_users,username,' . $hydroponicUser->id,
            'gender'    => 'required|string|in:' . collect(array_column(\App\Enums\UserGender::cases(), 'value'))->join(','),
            'phone_number' => 'required|string|regex:/^8([0-9]{8,})$/|unique:hydroponic_users,phone_number,' . $hydroponicUser->id,
            'address' => 'required|string|max:2000',
        ]);

        $hydroponicUser->update(
            $request->only([
                'name',
                'email',
                'username',
                'gender',
                'phone_number',
                'address',
            ])
        );

        return response()
            ->json([
                'message' => 'Berhasil disimpan!'
            ]);
    }

    public function updatePicture(Request $request) : JsonResponse
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpg,png|max:5048',
        ]);

        $hydroponicUser = HydroponicUser::find($this->hydroponicGuard()->id());

        $picture = image_intervention($request->file('picture'), $this->filePath, 1/1);

        if(File::exists(public_path($hydroponicUser->picture))){
            File::delete(public_path($hydroponicUser->picture));
        }

        $hydroponicUser->picture = $picture;
        $hydroponicUser->save();

        return response()
            ->json([
                'message' => 'Berhasil disimpan!'
            ]);
    }

    public function updatePassword(Request $request) : JsonResponse
    {
        $request->validate([
            'old_password' => 'required|string|current_password:hydroponic',
            'password' => 'required|string|min:8|confirmed|regex:/^[A-Za-z0-9!@#$%&_.]+$/'
        ]);

        $hydroponicUser = HydroponicUser::find($this->hydroponicGuard()->id());

        $hydroponicUser->password = Hash::make($request->password);
        $hydroponicUser->save();

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    private function hydroponicGuard(): \Illuminate\Contracts\Auth\Guard | \Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard('hydroponic');
    }
}
