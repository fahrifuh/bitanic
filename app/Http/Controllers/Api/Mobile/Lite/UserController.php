<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show() {
        return response()->json([
            'user' => auth()->guard('lite')->user()
        ]);
    }

    public function update(Request $request) : JsonResponse {
        $user = auth()->guard('lite')->user();

        $request->validate([
            'nik'           => 'required|integer|digits:16|unique:lite_users,nik,'. $user->id,
            'birth_date'     => 'required|date|date_format:Y-m-d',
            'address'        => 'required|string|max:500',
            'phone_number' => 'required|regex:/^628([0-9]{10,11})$/|digits_between:13,14|unique:lite_users,phone_number,' . $user->id,
            'name' => 'required|string|max:255'
        ]);

        $user->update($request->only([
            'nik',
            'birth_date',
            'address',
            'phone_number',
            'name',
        ]));

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function updatePicture(Request $request) : JsonResponse {
        $request->validate([
            'picture' => 'required|image|mimes:jpg,png|max:20480',
        ]);

        $user = auth()->guard('lite')->user();

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/lite-users/', 1/1);

        if(File::exists(public_path($user->picture))){
            File::delete(public_path($user->picture));
        }

        $user->update(['picture' => $picture]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function updatePassword(Request $request) : JsonResponse {
        $request->validate([
            'password_old' => 'required|string|current_password:lite',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = auth()->guard('lite')->user();

        // if (!Hash::check($request->old_password, $user->password)) {
        //     return response()->json([
        //         'message' => 'Password lama tidak sesuai! Harap ingat kembali atau hubungi admin'
        //     ], 401);
        // }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }
}
