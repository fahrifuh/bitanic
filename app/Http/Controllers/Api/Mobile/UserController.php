<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionApplication;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile()
    {
        $data = User::query()
            ->select(['id', 'name', 'phone_number', 'role', 'bitanic_plus'])
            ->with(['farmer' => function ($q) {
                $q->select(['id', 'full_name', 'nik', 'gender', 'birth_date', 'address', 'referral_code', 'picture', 'user_id', 'is_ktp_validated', 'ktp'])
                    ->with([
                        'shop' => function ($shop) {
                            $shop->select('id', 'name', 'picture', 'address', 'latitude', 'longitude', 'farmer_id')
                                ->withCount(['products']);
                        }
                    ])
                    ->withCount([
                        'shop as shop_status'
                    ]);
            }])
            ->find(auth()->user()->id);

        return response()->json([
            'data'  => $data,
            'message'   => "Data profile user!",
            'status' => 200
        ], 200);
    }

    public function detailAkun()
    {
        $data = User::query()
            ->select(['id', 'name', 'phone_number', 'role', 'bitanic_plus'])
            ->with(['farmer' => function ($q) {
                $q->select(['id', 'full_name', 'nik', 'gender', 'birth_date', 'address', 'referral_code', 'picture', 'user_id']);
            }])
            ->find(auth()->user()->id);

        $newData = (object) [
            'id' => $data->id,
            'name' => $data->name,
            'phone_number' => $data->phone_number,
            'role' => $data->role,
            'bitanic_plus' => $data->bitanic_plus,
            'petani' => (object) [
                'id' => $data->farmer->id,
                'nama_lengkap' => $data->farmer->full_name,
                'nik' => $data->farmer->nik,
                'jenis_kelamin' => $data->farmer->gender,
                'tgl_lahir' => $data->farmer->birth_date,
                'alamat' => $data->farmer->address,
                'kode_referral' => $data->farmer->referral_code,
                'foto' => $data->farmer->picture,
                'user_id' => $data->id
            ]
        ];

        return response()->json([
            'data'  => $newData,
            'message'   => "Data profile user!",
            'status' => 200
        ], 200);
    }

    public function updateName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $id = auth()->user()->id;

        $user = User::find($id);

        $user->update([
            'name' => $request->name
        ]);

        $user->farmer->update([
            'full_name' => $request->name
        ]);

        return response()->json([
            'message' => "Nama berhasil diupdate!",
            'status' => 200
        ], 200);
    }

    public function updatePhoneNumber(Request $request)
    {
        $id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            'phone_number'      => 'required|string|unique:users,phone_number,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $user = User::find($id);

        $user->update([
            'phone_number' => $request->phone_number
        ]);

        return response()->json([
            'message' => "Nomor HP berhasil diupdate!",
            'status' => 200
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'      => 'required|string',
            'new_password'      => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $user = User::find(auth()->user()->id);

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => "Password berhasil diupdate!",
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => "Password lama yang anda masukkan tidak cocok!",
            'status' => 400
        ], 400);
    }

    public function pengajuanHapusAkun(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alasan'      => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        if (AccountDeletionApplication::where('user_id', auth()->user()->id)->first()) {
            return response()->json([
                'message' => "Anda sudah mengajukan untuk menghapus akun!",
                'status' => 400
            ], 400);
        }

        AccountDeletionApplication::create([
            'reason' => $request->alasan,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'message' => "Pengajuan berhasil dikirim!",
            'status' => 200
        ], 200);
    }

    public function aktivasiBitanicPlus(Request $request)
    {
        if (auth()->user()->bitanic_plus == 1) {
            return response()->json([
                'message' => (object) [
                    'text' => ['Anda sudah ada dalam bitanic plus'],
                ],
                'status' => 400
            ], 400);
        }
        $id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            'nik'           => 'required|integer|digits:16|unique:farmers,nik,' . auth()->user()->farmer->id,
            'birth_date'     => 'required|date',
            'address'        => 'required|string|max:500',
            'gender' => 'required|in:l,p',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $user = User::with('farmer')->find($id);

        $user->update([
            'bitanic_plus' => 1
        ]);

        $user->farmer->update([
            'nik' => $request->nik,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'gender' => $request->gender
        ]);

        return response()->json([
            'message' => "Bitanic plus anda berhasil diaktivasi!",
            'status' => 200
        ], 200);
    }

    public function getKtp() {
        $id = auth()->user()->id;
        $user = User::query()
            ->with('farmer')
            ->find($id);

        return response()
            ->file(storage_path('app/' . $user->farmer->ktp));
    }

    public function updateKtp(Request $request): JsonResponse
    {
        $id = auth()->user()->id;
        $user = User::query()
            ->with('farmer')
            ->find($id);

        if ($user->farmer->ktp && $user->farmer->is_ktp_validated == 1) {
            return response()
                ->json([
                    'message' => 'KTP sudah tervalidasi!'
                ]);
        }

        $request->validate([
            'ktp'   => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $image = $request->file('ktp');
        $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs('ktp/user', $request->file('ktp'), $name);
        $user->farmer->ktp = $path;
        $user->farmer->is_ktp_validated = null;
        $user->push();

        return response()
            ->json([
                'message' => 'Berhasil disimpan! Harap tunggu untuk verifikasi KTP anda'
            ]);
    }
}
