<?php

namespace App\Http\Controllers\Api\Mobile\Hydroponic;

use App\Http\Controllers\Controller;
use App\Models\HydroponicUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'             => 'required|string|email',
            'password'          => 'required|string|max:255',
            'firebase_token'    => 'nullable|string|max:255',
        ], [
            'phone_number.required' => 'No HP wajib diisi!',
            'phone_number.numeric' => 'No HP harus berupa angka!',
            'password.required' => 'Password wajib diisi!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors(),
                'status' => 401
            ], 401);
        }

        $user = HydroponicUser::query()
            ->where('email', $request->email)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($request->firebase_token) {
                $user->firebase_token =  $request->firebase_token;
                $user->save();
            }

            $success['message'] = "Success";
            $success['token'] =  $user->createToken(Str::random(10))->plainTextToken;
            $success['user'] = $user;
            $success['status'] = 200;

            return response()->json($success, 200);
        }

        return response()->json([
            'message' => "Login gagal! \n Email atau password salah!",
            'status' => 401
        ], 404);
    }

    public function logout() : JsonResponse {
        $user = HydroponicUser::find($this->hydroponicGuard()->id());

        $user->tokens()->delete();

        return response()
            ->json([
                'message' => 'Berhasil logout'
            ]);
    }

    private function hydroponicGuard(): \Illuminate\Contracts\Auth\Guard | \Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard('hydroponic');
    }
}
