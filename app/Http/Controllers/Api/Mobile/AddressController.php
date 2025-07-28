<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'List data alamat pengguna',
            'status' => 200,
            'address' => auth()->user()->addresses
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'latitude'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'   => 'required|string|max:5000',
            'recipient_name'    => 'required|string|max:100',
            'recipient_phone_number'    => 'required|regex:/^62\d{10,12}$/',
            'detail'    => 'nullable|string|max:1000',
            'postal_code'    => 'required|numeric|digits:5',
            'province_id'    => 'nullable|integer|min:0',
            'city_id'    => 'nullable|integer|min:0',
        ], [
            'recipient_phone_number.regex' => 'Format nomor handphone harus diawali dengan 62 dan nomor harus berjumlah di antara 12 sampai 14',
            'recipient_name.max' => 'Nama penerima tidak bisa lebih dari 100 karakter!',
            'postal_code.digits' => 'Kode Pos harus berjumlah 5 digit angka!',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        Address::create(
            $request->only(['address', 'postal_code', 'recipient_name', 'recipient_phone_number', 'latitude', 'longitude', 'detail', 'province_id', 'city_id']) + [
                'user_id' => auth()->user()->id
            ]
        );

        return response()->json([
            'message' => 'Data berhasil disimpan!',
            'status' => 200,
        ], 200);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorize('user-address', $address);

        return response()->json([
            'message' => 'Alamat pengguna',
            'status' => 200,
            'address' => $address
        ], 200);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        $this->authorize('user-address', $address);

        $v = Validator::make($request->all(), [
            'latitude'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'   => 'required|string|max:5000',
            'recipient_name'    => 'required|string|max:100',
            'recipient_phone_number'    => 'required|regex:/^62\d{10,12}$/',
            'detail'    => 'nullable|string|max:1000',
            'postal_code'    => 'required|numeric|digits:5',
            'province_id'    => 'nullable|integer|min:0',
            'city_id'    => 'nullable|integer|min:0',
        ], [
            'recipient_phone_number.regex' => 'Format nomor handphone harus diawali dengan 62 dan nomor harus berjumlah di antara 12 sampai 14',
            'recipient_name.max' => 'Nama penerima tidak bisa lebih dari 100 karakter!',
            'postal_code.digits' => 'Kode Pos harus berjumlah 5 digit angka!',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $address->update(
            $request->only('address', 'postal_code', 'recipient_name', 'recipient_phone_number', 'latitude', 'longitude', 'detail', 'province_id', 'city_id')
        );

        return response()->json([
            'message' => 'Data berhasil disimpan!',
            'status' => 200,
        ], 200);
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('user-address', $address);

        $address->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus!',
            'status' => 200,
        ], 200);
    }
}
