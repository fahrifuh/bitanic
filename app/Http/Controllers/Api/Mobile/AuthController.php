<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number'  => 'required|numeric',
            'password'      => 'required|string|max:255',
            'firebase_token' => 'nullable|string|max:255',
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

        $user = User::where('phone_number', $request->phone_number)->first();

        if ($user && $user->role !== 'farmer') {
            return abort(403, 'Anda tidak memiliki hak akses ke konten ini;');
        }

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);

            if ($request->firebase_token) {
                $user->firebase_token =  $request->firebase_token;
                $user->save();
            }

            $auth = Auth::user();

            $success['message'] = "Success";
            $success['token'] =  $user->createToken(Str::random(10))->plainTextToken;
            $success['user'] = $user->loadMissing('farmer');
            $success['status'] = 200;

            activity()
                ->event('login')
                ->log('login mobile');

            return response()->json($success, 200);
        }

        return response()->json([
            'message' => "Login gagal! \n No HP atau password salah!",
            'status' => 401
        ], 404);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone_number'  => 'required|regex:/^628([0-9]{9,13})$/|unique:users,phone_number',
            'password'      => 'required|string|min:8',
            'no_seri'       => 'sometimes|nullable|string|max:100',
            'firebase_token' => 'nullable|string|max:255',
            'ktp'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'phone_number.regex' => 'Format no telepon harus diawali dengan 628xxxxxxxxxx',
            'phone_number.digits_between' => 'Digits telepon diantara 11, 13 char',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors'=>$validator->errors(),
                'status' => 401
            ], 401);
        }

        $input = $request->all();
        $input['password'] =  Hash::make($input['password']);
        $user = User::create([
            'name' => $request->name,
            'phone_number'  => $request->phone_number,
            'password' => $input['password'],
            'role' => 'farmer',
            'bitanic_plus' => 0
        ]);

        $petani = Farmer::create([
            'full_name' => $request->name,
            'gender' => 'l',
            'user_id' => $user->id,
            'type' => 1
        ]);


        if ($request->hasFile('ktp')) {
            $image = $request->file('ktp');
            $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $image->extension();
            $path = Storage::putFileAs('ktp/user', $request->file('ktp'), $name);
            $petani->update([
                'ktp' => $path
            ]);
        }

        $checkDevice = null;

        if ($request->no_seri) {
            $checkDevice = Device::query()
                ->whereNull('farmer_id')
                ->whereNull('activate_date')
                ->where('device_series', $request->no_seri)
                ->first();

            $success['message'] = "Perangkat Tidak diketahui! Harap masukan perangkat yang benar";
        }

        if ($request->firebase_token) {
            $user->firebase_token =  $request->firebase_token;
            $user->save();
        }

        if ($checkDevice) {
            $checkDevice->activate_date = now('Asia/Jakarta');
            $checkDevice->farmer_id = $petani->id;
            $checkDevice->save();

            $success['message'] = "Berhasil registrasi perangkat!";
        }

        $success['token'] =  $user->createToken(Str::random(10))->plainTextToken;
        $success['status'] = 200;
        $success['user'] = $user->loadMissing('farmer');

        return response()->json($success, 200);
    }

    public function checkPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number'  => 'required|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 401
            ], 401);
        }

        return response()->json([
            'message' => "Nomor HP dapat digunakan!",
            'status' => 200
        ], 200);
    }

    public function logout()
    {
        $user = User::find(Auth::user()->id);

        if ($user->firebase_token) {
            $user->firebase_token = null;
            $user->save();
        }

        $user->tokens()->delete();

        activity()
            ->event('logout')
            ->log('logout mobile');

        return response()->json([
            'message'   => "User logout!",
            'status' => 200
        ], 200);
    }

    public function forgotPassword(Request $request) : JsonResponse {
        $value = $request->header('X-Forgot-Password');

        if ($value !== "bbdba5a0-6859-4783-b742-8098084499b8") {
            return response()->json([
                'message' => 'Header is not match!'
            ], 422);
        }

        $validated = $request->validate([
            'phone_number' => 'required|regex:/^628([0-9]{9,13})$/',
            'new_password' => 'required|string|min:8',
        ]);

        $user = User::query()
            ->where('phone_number', $validated['phone_number'])
            ->firstOrFail();

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diupdate, silahkan login kembali'
        ]);
    }
}
