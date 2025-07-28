<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\FarmerGroup;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountSettingController extends Controller
{
    public function index(Request $request) : View {
        $user = User::query()
            ->with([
                'farmer',
                'subdistrict:id,subdis_name,dis_id',
                'subdistrict.district:id,dis_name,city_id',
                'subdistrict.district.city:id,city_name,province_id',
                'subdistrict.district.city.province:id,prov_name',
            ])
            ->findOrFail(auth()->id());

        return view('bitanic.account-setting.account', [
            'user' => $user
        ]);
    }

    public function showKtp() {
        $id = auth()->user()->id;
        $user = User::query()
            ->with('farmer')
            ->find($id);

        return response()
            ->file(storage_path('app/' . $user->farmer->ktp));
    }

    public function update(Request $request) {
        $user = User::query()
            ->with('farmer')
            ->findOrFail(auth()->id());

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'required|numeric|digits_between:10,12|unique:users,phone_number,' . $user->id,
            'nik' => 'required|integer|digits:16|unique:farmers,nik,' . $user->farmer->id,
            'gender' => 'required|in:l,p',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:1000',
            'picture' => 'nullable|image|mimes:jpg,png|max:2048',
            'subdistrict' => 'required|exists:subdistricts,id',
            'farmer_group' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !FarmerGroup::find($value)) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ]
        ]);

        $picture_new = [];
        $picture_old = [];

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/farmers/', 1/1);

            if (File::exists(public_path($user->farmer->picture))) {
                File::delete(public_path($user->farmer->picture));
            }

            $user->farmer->picture = $foto;
            $user->farmer->save();
            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $original = $user->getOriginal();
        $original_farmer = $user->farmer->getOriginal();

        $user->update(
            $request->only(['name']) + [
                'phone_number' => '62' . (int) $request->phone_number,
                'subdis_id' => $request->subdistrict
            ],
        );

        $user->farmer->update(
            $request->only(['nik', 'gender', 'birth_date', 'address']) + [
                'full_name' => $request->name,
                'group_id' => $request->farmer_group ? $request->farmer_group : null
            ],
        );

        $changes = collect($user->getChanges());
        $old = collect($original)->only($changes->keys());
        $changes_farmer = collect($user->farmer->getChanges());
        $old_farmer = collect($original_farmer)->only($changes_farmer->keys());

        activity()
            ->performedOn($user->farmer)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['updated_at', 'password'])
                                ->merge(
                                    $old_farmer
                                        ->except(['updated_at', 'picture'])
                                        ->merge($picture_old)
                                        ->toArray(),
                                )
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['updated_at', 'password'])
                                ->merge(
                                    $changes_farmer
                                        ->except(['updated_at', 'picture'])
                                        ->merge($picture_new)
                                        ->toArray(),
                                )
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');
        return back()->with('success', 'Berhasil disimpan');
    }

    public function updateKtp(Request $request) {
        $id = auth()->user()->id;
        $user = User::query()
            ->with('farmer')
            ->find($id);

        if ($user->farmer->ktp && $user->farmer->is_ktp_validated == 1) {
            return redirect()
                ->back()
                ->with('failed-ktp', 'KTP sudah diverifikasi');
        }

        $request->validate([
            'ktp'   => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        if (Storage::exists($user->farmer->ktp)) {
            Storage::delete($user->farmer->ktp);
        }

        $image = $request->file('ktp');
        $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs('ktp/user', $request->file('ktp'), $name);
        $user->farmer->ktp = $path;
        $user->farmer->is_ktp_validated = null;
        $user->push();

        return redirect()
            ->back()
            ->with('success-ktp', 'Berhasil disimpan! Harap tunggu untuk verifikasi KTP anda');
    }

    public function updateProfilePicture(Request $request) : JsonResponse {
        $user = User::query()
            ->with('farmer:id,user_id,picture')
            ->findOrFail(auth()->id());

        $request->validate([
            'picture' => ['required', 'image', 'mimes:png,jpg', 'max:10240']
        ]);

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/farmers/', 1/1);

            if (File::exists(public_path($user->farmer->picture))) {
                File::delete(public_path($user->farmer->picture));
            }

            $user->farmer->picture = $foto;
            $user->farmer->save();
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function destroyAccount(Request $request) {
        // dd($request->all());
        $user = User::query()
            ->with('farmer:id,user_id,nik')
            ->findOrFail(auth()->id());

        $accountActivation = $request->accountActivation;

        if (!$accountActivation || $accountActivation != 'on') {
            return back()->withErrors([
                'accountActivation' => [
                    "Harap konfirmasi bahwa anda ingin menghapus akun"
                ]
            ]);
        }

        activity()
            ->performedOn($user)
            ->withProperties(['name', $user->name, 'phone_number' => $user->phone_number, 'nik' => $user->farmer->nik])
            ->event('deleted')
            ->log('deleted');

        auth()->guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('login');
    }
}
