<?php

namespace App\Http\Controllers\Hydroponic;

use App\Http\Controllers\Controller;
use App\Models\HydroponicUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected string $filePath = 'bitanic-photo/hydroponic/user/';
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $hydroponicUsers = HydroponicUser::query()
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.hydroponic.user.index', compact('hydroponicUsers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        $userGenders = \App\Enums\UserGender::cases();
        return view('bitanic.hydroponic.user.create', compact('userGenders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hydroponic_users,email',
            'username' => 'required|string|max:255|alpha_dash|unique:hydroponic_users,username',
            'password' => 'required|string|min:8|max:20|confirmed|regex:/^[A-Za-z0-9!@#$%&_.]+$/',
            'gender'    => 'required|string|in:' . collect(array_column(\App\Enums\UserGender::cases(), 'value'))->join(','),
            'phone_number' => 'required|string|regex:/^8([0-9]{8,})$/|unique:hydroponic_users,phone_number',
            'address' => 'required|string|max:2000',
            'picture' => 'required|image|mimes:jpg,png|max:5048',
        ]);

        $picture = image_intervention($request->file('picture'), $this->filePath, 1/1);

        HydroponicUser::create(
            $request->only([
                'name',
                'email',
                'username',
                'gender',
                'phone_number',
                'address',
            ]) + [
                'picture' => $picture,
                'referral_code' => Str::random(9),
                'password' => Hash::make($request->password),
            ]
        );

        return redirect()
            ->route('bitanic.hydroponic.user.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HydroponicUser  $hydroponicUser
     */
    public function show(HydroponicUser $hydroponicUser): View
    {
        return view('bitanic.hydroponic.user.show', compact('hydroponicUser'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HydroponicUser  $hydroponicUser
     * @return \Illuminate\Http\Response
     */
    public function edit(HydroponicUser $hydroponicUser)
    {
        $userGenders = \App\Enums\UserGender::cases();
        return view('bitanic.hydroponic.user.edit', [
            'hydroponicUser' => $hydroponicUser,
            'userGenders' => $userGenders,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HydroponicUser  $hydroponicUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HydroponicUser $hydroponicUser)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hydroponic_users,email,' . $hydroponicUser->id,
            'username' => 'required|string|max:255|alpha_dash|unique:hydroponic_users,username,' . $hydroponicUser->id,
            'gender'    => 'required|string|in:' . collect(array_column(\App\Enums\UserGender::cases(), 'value'))->join(','),
            'phone_number' => 'required|string|regex:/^8([0-9]{8,})$/|unique:hydroponic_users,phone_number,' . $hydroponicUser->id,
            'address' => 'required|string|max:2000',
            'picture' => 'nullable|image|mimes:jpg,png|max:5048',
        ]);

        $picture = $hydroponicUser->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), $this->filePath, 1/1);

            if(File::exists(public_path($hydroponicUser->picture))){
                File::delete(public_path($hydroponicUser->picture));
            }
        }

        $hydroponicUser->update(
            $request->only([
                'name',
                'email',
                'username',
                'gender',
                'phone_number',
                'address',
            ]) + [
                'picture' => $picture
            ]
        );

        return redirect()
            ->route('bitanic.hydroponic.user.show', $hydroponicUser->id)
            ->with('success', 'Berhasil disimpan');
    }

    public function editPassword(HydroponicUser $hydroponicUser) : View {
        return view('bitanic.hydroponic.user.edit-password', compact('hydroponicUser'));
    }

    public function updatePassword(Request $request, HydroponicUser $hydroponicUser) : RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed|regex:/^[A-Za-z0-9!@#$%&_.]+$/'
        ]);

        $hydroponicUser->password = Hash::make($request->password);
        $hydroponicUser->save();

        return redirect()
            ->route('bitanic.hydroponic.user.show', $hydroponicUser->id)
            ->with('success', 'Password baru berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HydroponicUser  $hydroponicUser
     */
    public function destroy(HydroponicUser $hydroponicUser): JsonResponse | RedirectResponse
    {
        if(File::exists(public_path($hydroponicUser->picture))){
            File::delete(public_path($hydroponicUser->picture));
        }

        $hydroponicUser->delete();

        $message = 'Berhasil dihapus';

        session()->flash('success', $message);

        if (request()->wantsJson()) {
            return response()
                ->json([
                    'message' => $message
                ]);
        }

        return redirect()
            ->route('bitanic.hydroponic.user.index');
    }
}
