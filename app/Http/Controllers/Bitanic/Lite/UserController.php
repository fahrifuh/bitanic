<?php

namespace App\Http\Controllers\Bitanic\Lite;

use App\Exports\FarmerLiteExport;
use App\Http\Controllers\Controller;
use App\Models\LiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $lite_users = LiteUser::query()
            ->select(['id', 'name', 'nik', 'phone_number', 'picture', 'gender'])
            ->when($search, function ($query, $search) {
                $search = '%' . trim($search) . '%';
                return $query->where('name', 'LIKE', $search)
                    ->orWhere('nik', 'LIKE', $search)
                    ->orWhere('phone_number', 'LIKE', $search);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('bitanic.lite.farmer.index', compact('lite_users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bitanic.lite.farmer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:1000',
            'birth_date' => 'required|date|date_format:Y-m-d',
            'nik' => 'required|regex:/^([0-9])$/|digits:16|unique:lite_users,nik',
            'phone_number' => 'required|string|regex:/^([0-9]{11,13})$/',
            'gender' => 'required|string|in:male,female',
            'name' => 'required|string|max:255',
            'picture' => 'required|image|mimes:jpg,png|max:10240',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nik.regex' => 'Isi NIK harus berupa angka!'
        ]);

        $check_phone_number = LiteUser::query()
            ->where('phone_number', '62' . $request->phone_number)
            ->count();

        if ($check_phone_number > 0) {
            return back()->withErrors([
                'messages' => ['Nomor Handphone tidak bisa dipakai']
            ]);
        }

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/lite-users/', 1/1);

        LiteUser::create($request->only([
            'address',
            'birth_date',
            'nik',
            'gender',
            'name',
        ]) + [
            'phone_number' => 62 . $request->phone_number,
            'picture' => $picture,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('bitanic.lite-user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LiteUser  $liteUser
     * @return \Illuminate\Http\Response
     */
    public function show(LiteUser $lite_user)
    {
        return view('bitanic.lite.farmer.show', compact('lite_user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LiteUser  $liteUser
     * @return \Illuminate\Http\Response
     */
    public function edit(LiteUser $liteUser)
    {
        return view('bitanic.lite.farmer.edit', compact('liteUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LiteUser  $liteUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LiteUser $liteUser)
    {
        $request->validate([
            'address' => 'required|string|max:1000',
            'birth_date' => 'required|date|date_format:Y-m-d',
            'nik' => 'required|numeric|digits:16|unique:lite_users,nik,' . $liteUser->id,
            'phone_number' => 'required|string|regex:/^([0-9]{11,13})$/',
            'gender' => 'required|string|in:male,female',
            'name' => 'required|string|max:255',
            'picture' => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        $check_phone_number = LiteUser::query()
            ->where('phone_number', '62' . $request->phone_number)
            ->where('id', '<>', $liteUser->id)
            ->count();

        if ($check_phone_number > 0) {
            return back()->withErrors([
                'messages' => ['Nomor Handphone tidak bisa dipakai']
            ]);
        }

        $columns = [
            'phone_number' => 62 . $request->phone_number,
        ];

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/lite-devices/', 16/9);

            if(File::exists(public_path($liteUser->picture))){
                File::delete(public_path($liteUser->picture));
            }

            $columns = array_merge($columns, [
                'picture' => $picture
            ]);
        }

        $liteUser->update($request->only([
            'address',
            'birth_date',
            'nik',
            'gender',
            'name',
        ]) + $columns);

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiteUser  $liteUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiteUser $liteUser)
    {
        if(File::exists(public_path($liteUser->picture))){
          File::delete(public_path($liteUser->picture));
        }

        $liteUser->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }

    public function editPassword(LiteUser $liteUser) {
        return view('bitanic.lite.farmer.edit-password', compact('liteUser'));
    }

    public function updatePassword(Request $request, LiteUser $liteUser)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $liteUser->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Berhasil disimpan');
    }

    public function exportExcel()
    {
        $liteUsers = LiteUser::query()
            ->orderBy('name')
            ->get();

        if (count($liteUsers) == 0) {
            return back()->withErrors([
                'users' => ['Tidak ada data user']
            ]);
        }

        return Excel::download(
            new FarmerLiteExport($liteUsers),
            now()->format('YmdHis') . '_lite_farmers.xlsx'
        );
    }
}
