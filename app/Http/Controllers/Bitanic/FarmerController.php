<?php

namespace App\Http\Controllers\Bitanic;

use App\Exports\FarmerExport;
use App\Http\Controllers\Controller;
use App\Models\BitanicProduct;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\Province;
use App\Models\User;
use Faker\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class FarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('farmers');

        $farmer = User::with(['farmer.bitanicProducts', 'subdistrict.district.city.province'])
            ->has('farmer')
            ->when((auth()->user()->role == 'admin' && auth()->user()->city_id != null), function ($query, $status) {
                return $query->whereHas('subdistrict.district', function ($query) {
                    $query->where('city_id', auth()->user()->city_id);
                });
            })
            ->when(request()->query('search'), function ($query, $a) {
                $search = request()->query('search');
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone_number', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('farmer', function ($fm) use ($search) {
                            $fm->where('nik', 'LIKE', '%' . $search . '%');
                        });
                });
            });

        $isZero = false;

        if (request()->query('province') && request()->query('province') == 'zero') {
            $province = request()->query('province');
            $farmer = $farmer->where(function ($fm) {
                $fm->whereNull('subdis_id');
            });
            $isZero = true;
        }

        if (!$isZero && request()->query('province') && is_numeric(request()->query('province'))) {
            $province = request()->query('province');
            $farmer = $farmer->where(function ($fm) use ($province) {
                $fm->whereHas('subdistrict.district.city', function ($query) use ($province) {
                    $query->where('province_id', $province);
                });
            });
        }
        if (!$isZero && request()->query('city') && request()->query('city') != 'all') {
            $city = request()->query('city');
            $farmer = $farmer->where(function ($fm) use ($city) {
                $fm->whereHas('subdistrict.district.city', function ($query) use ($city) {
                    $query->where('id', $city);
                });
            });
        }
        if (!$isZero && request()->query('district') && request()->query('district') != 'all') {
            $district = request()->query('district');
            $farmer = $farmer->where(function ($fm) use ($district) {
                $fm->whereHas('subdistrict.district', function ($query) use ($district) {
                    $query->where('id', $district);
                });
            });
        }
        if (!$isZero && request()->query('subdistrict') && request()->query('subdistrict') != 'all') {
            $subdistrict = request()->query('subdistrict');
            $farmer = $farmer->where(function ($fm) use ($subdistrict) {
                $fm->whereHas('subdistrict', function ($query) use ($subdistrict) {
                    $query->where('id', $subdistrict);
                });
            });
        }


        $data['data'] = $farmer->paginate(10)->withQueryString();
        $data['products'] = BitanicProduct::get();

        return view('bitanic.farmer.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'type' => 'required|integer|in:1,2',
            'category' => ['required', Rule::in(array_keys(farmerCategory()))],
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|unique:users,phone_number|numeric|digits_between:10,12',
            'nik' => 'required|integer|digits:16|unique:farmers,nik',
            'gender' => 'required|in:l,p',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:1000',
            'picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'subdistrict' => 'required|exists:subdistricts,id',
            'farmer_group' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !FarmerGroup::find($value)) {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
            'products' => 'nullable|array',
            'products.*' => 'exists:bitanic_products,id',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                401,
            );
        }

        $faker = Factory::create('id_ID');

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/farmers/');

        $user = User::create(
            $request->only(['name']) + [
                'phone_number' => '62' . (int) $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'farmer',
                'bitanic_plus' => 0,
                'subdis_id' => $request->subdistrict
            ],
        );

        $farmer = Farmer::create(
            $request->only(['nik', 'gender', 'birth_date', 'address', 'type', 'category']) + [
                'full_name' => $request->name,
                'picture' => $foto,
                'user_id' => $user->id,
                'referral_code' => $faker->unique()->regexify('[A-Za-z0-9]{6,9}'),
                'group_id' => $request->farmer_group ? $request->farmer_group : null
            ],
        );

        if ($request->filled('products')) {
            $farmer->bitanicProducts()->sync($request->input('products'));
        }

        activity()
            ->performedOn($farmer)
            ->withProperties(
                collect($farmer)
                    ->except(['id', 'picture', 'referral_code', 'user_id', 'created_at', 'updated_at'])
                    ->merge(['phone_number' => $user->phone_number]),
            )
            ->event('created')
            ->log('created');

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $farmer)
    {
        Gate::authorize('farmers');

        $farmer->load(['farmer']);

        $devices = Device::query()
            ->with([
                'garden:id,land_id',
                'garden.land:id,name'
            ])
            ->has('garden')
            ->where('farmer_id', $farmer->farmer->id)
            ->where('category', 'controller')
            ->get();

        return view('bitanic.farmer.show', [
            'user' => $farmer,
            'devices' => $devices
        ]);
    }

    public function edit($id): View
    {
        $user = User::query()
            ->with(['farmer', 'subdistrict.district.city.province'])
            ->has('farmer')
            ->firstWhere('id', $id);

        if (!$user) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data pengguna tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        return view('bitanic.farmer.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::query()
            ->with(['farmer.bitanicProducts', 'subdistrict.district'])
            ->has('farmer')
            ->firstWhere('id', $id);

        if (!$user) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data pengguna tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null && $user->subdistrict->district->city_id != auth()->user()->city_id) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Anda tidak bisa mengubah data petani ini karena wilayah kabupaten/kota petani tidak sama dengan anda!'],
                    ],
                ],
                404,
            );
        }

        $v = Validator::make($request->all(), [
            'type' => 'required|integer|in:1,2',
            'category' => ['required', Rule::in(array_keys(farmerCategory()))],
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'required|numeric|digits_between:10,12|unique:users,phone_number,' . $id,
            'nik' => 'required|integer|digits:16|unique:farmers,nik,' . $user->farmer->id,
            'gender' => 'required|in:l,p',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:1000',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'subdistrict' => 'required|exists:subdistricts,id',
            'farmer_group' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !FarmerGroup::find($value)) {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
            'products' => 'nullable|array',
            'products.*' => 'exists:bitanic_products,id',
        ]);

        if ($v->fails()) {
            if ($request->wantsJson()) {
                return response()->json(
                    [
                        'messages' => $v->errors(),
                    ],
                    400,
                );
            }

            return back()->withErrors($v->errors());
        }

        $picture_new = [];
        $picture_old = [];

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/crops/', 1 / 1);

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
            $request->only(['nik', 'gender', 'birth_date', 'address', 'type', 'category']) + [
                'full_name' => $request->name,
                'group_id' => $request->farmer_group ? $request->farmer_group : null
            ],
        );

        if ($request->filled('products')) {
            $user->farmer->bitanicProducts->sync($request->input('products'));
        }

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

        if ($request->wantsJson()) {
            return response()->json(
                [
                    'message' => 'Berhasil',
                ],
                200,
            );
        }

        return redirect()->route('bitanic.farmer.show', $user->id)->with('success', 'Berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::query()
            ->has('farmer')
            ->firstWhere('id', $id);

        if (!$user) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data pengguna tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null && $user->subdistrict->district->city_id != auth()->user()->city_id) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Anda tidak bisa mengubah data petani ini karena wilayah kabupaten/kota petani tidak sama dengan anda!'],
                    ],
                ],
                404,
            );
        }

        if (\File::exists(public_path($user->farmer->picture))) {
            \File::delete(public_path($user->farmer->picture));
        }

        activity()
            ->performedOn($user->farmer)
            ->withProperties(['name', $user->name, 'phone_number' => $user->phone_number, 'nik' => $user->farmer->nik])
            ->event('deleted')
            ->log('deleted');

        $user->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    public function exportExcel()
    {
        $isZero = request()->query('province') == 'zero' ? true : false;

        $users = User::query()
            ->with([
                'farmer:id,user_id,gender,nik,birth_date,address',
                'subdistrict.district.city.province',
            ])
            ->where('role', 'farmer')
            ->when($isZero == true, function ($query, $status) {
                $query->whereNull('subdis_id');
            })
            ->when(
                (!$isZero && request()->query('province') && request()->query('province') != 'all'),
                function ($query, $status) {
                    $query->whereHas('subdistrict.district.city', function ($query) {
                        $query->where('province_id', request()->query('province'));
                    });
                }
            )
            ->when(
                (!$isZero && request()->query('city') && request()->query('city') != 'all'),
                function ($query, $status) {
                    $query->whereHas('subdistrict.district.city', function ($query) {
                        $query->where('id', request()->query('city'));
                    });
                }
            )
            ->when(
                (!$isZero && request()->query('district') && request()->query('district') != 'all'),
                function ($query, $status) {
                    $query->whereHas('subdistrict.district', function ($query) {
                        $query->where('id', request()->query('district'));
                    });
                }
            )
            ->when(
                (!$isZero && request()->query('subdistrict') && request()->query('subdistrict') != 'all'),
                function ($query, $status) {
                    $query->whereHas('subdistrict', function ($query) {
                        $query->where('id', request()->query('subdistrict'));
                    });
                }
            )
            ->orderBy('name')
            ->get();

        if (count($users) == 0) {
            return back()->withErrors([
                'users' => ['Tidak ada data user']
            ]);
        }

        return Excel::download(new FarmerExport($users), now()->format('YmdHis') . '_farmers.xlsx');
    }
}
