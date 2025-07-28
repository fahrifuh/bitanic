<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number'  => 'required|numeric',
            'password'      => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors(),
                'status' => 401
            ], 401);
        }

        $check = LiteUser::where('phone_number', $request->phone_number)->first();

        if ($check && Hash::check($request->password, $check->password)) {
            // Auth::guard('lite')->login($check);

            // $user = Auth::guard('lite')->user();
            $token = $check->createToken(Str::random(10))->plainTextToken;

            $success['message'] = "Success";
            $success['token'] =  $token;
            $success['user'] = $check;
            $success['status'] = 200;

            activity()
                ->event('login')
                ->log('login mobile');

            return response()->json($success, 200);
        }

        return response()->json([
            'message' => "Login failed! \n Phone number or password is incorrect!",
            'status' => 401
        ], 404);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'gender' => 'required|string|in:male,female',
            'phone_number'  => 'required|regex:/^628([0-9]{10,11})$/|digits_between:13,14|unique:lite_users,phone_number',
            'password'      => 'required|string|min:8',
            'no_seri'       => 'sometimes|nullable|string|max:100'
        ], [
            'phone_number.regex' => 'Format no telepon harus diawali dengan 628xxxxxxxxxx',
            'phone_number.digits_between' => 'Digits telepon diantara 13, 14 char',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors(),
                'status' => 401
            ], 401);
        }

        $user = LiteUser::create([
            'name' => $request->name,
            'phone_number'  => $request->phone_number,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
        ]);

        $success['token'] =  $user->createToken(Str::random(10))->plainTextToken;
        $success['status'] = 200;
        $success['user'] = $user;

        return response()->json($success, 200);
    }

    public function checkPhoneNumber(Request $request)
    {
        $request->validate([
            'phone_number'  => 'required|unique:lite_users,phone_number',
        ]);

        return response()->json([
            'message' => "Nomor HP dapat digunakan!",
            'status' => 200
        ], 200);
    }

    public function logout()
    {
        auth()->guard('lite')->user()->tokens()->delete();

        activity()
            ->event('logout')
            ->log('logout mobile');

        return response()->json([
            'message'   => "User logout!",
            'status' => 200
        ], 200);
    }
}
