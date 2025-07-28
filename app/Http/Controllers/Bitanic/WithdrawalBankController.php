<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WithdrawalBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $withdrawalBanks = WithdrawalBank::query()
            ->select(['id', 'name', 'picture'])
            ->orderBy('name')
            ->paginate(10);

        return view('bitanic.withdrawal-bank.index', compact('withdrawalBanks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bitanic.withdrawal-bank.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:withdrawal_banks,code',
            'description' => 'nullable|string|max:500',
            'picture' => 'required|image|mimes:jpg,png|max:10240',
        ]);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/withdrawal-banks/', 3/1);

        WithdrawalBank::create(
            $request->only(['name', 'code', 'description']) +
            [
                'picture' => $picture,
            ]
        );

        return redirect()->route('bitanic.withdrawal-bank.index')->with('success', 'berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WithdrawalBank  $withdrawalBank
     * @return \Illuminate\Http\Response
     */
    public function show(WithdrawalBank $withdrawalBank)
    {
        return view('bitanic.withdrawal-bank.show', compact('withdrawalBank'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WithdrawalBank  $withdrawalBank
     * @return \Illuminate\Http\Response
     */
    public function edit(WithdrawalBank $withdrawalBank)
    {
        return view('bitanic.withdrawal-bank.edit', compact('withdrawalBank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WithdrawalBank  $withdrawalBank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WithdrawalBank $withdrawalBank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:withdrawal_banks,code,' . $withdrawalBank->id,
            'description' => 'nullable|string|max:500',
            'picture' => 'nullable|image|mimes:jpg,png|max:10240',
        ]);


        $picture = $withdrawalBank->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/withdrawal-banks/', 3/1);

            if(File::exists(public_path($withdrawalBank->picture))){
                File::delete(public_path($withdrawalBank->picture));
            }
        }

        $withdrawalBank->update(
            $request->only(['name', 'code', 'description']) +
            [
                'picture' => $picture,
            ]
        );

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WithdrawalBank  $withdrawalBank
     * @return \Illuminate\Http\Response
     */
    public function destroy(WithdrawalBank $withdrawalBank)
    {
        if(File::exists(public_path($withdrawalBank->picture))){
            File::delete(public_path($withdrawalBank->picture));
        }

        $withdrawalBank->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }
}
