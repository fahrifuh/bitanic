<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Rules\BankFees;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BankController extends Controller
{
    public function index(): View
    {
        $banks = Bank::query()
            ->select(['id', 'name', 'picture', 'fees'])
            ->orderBy('name')
            ->paginate(10);

        return view('bitanic.bank.index', compact('banks'));
    }

    public function create(): View
    {
        return view('bitanic.bank.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:banks,code',
            'description' => 'nullable|string|max:500',
            'picture' => 'required|image|mimes:jpg,png|max:10240',
            'fees' => 'required|array|min:1|max:2',
            'fees.*.type' => 'required|integer|in:0,1',
            'fees.*.fee' => 'exclude_if:fees.*.type,1|required|integer|min:0',
            'fees.*.fee' => 'exclude_if:fees.*.type,0|required|numeric|min:0|max:100',
        ]);

        $fees = $this->formatInputFees($request->fees);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/banks/', 3/1);

        Bank::create(
            $request->only(['name', 'code', 'description']) +
            [
                'picture' => $picture,
                'fees' => $fees,
            ]
        );

        return redirect()->route('bitanic.bank.index')->with('success', 'berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(Bank $bank): View
    {
        return view('bitanic.bank.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Bank $bank): View
    {
        return view('bitanic.bank.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Bank $bank): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:banks,code,' . $bank->id,
            'description' => 'nullable|string|max:500',
            'picture' => 'nullable|image|mimes:jpg,png|max:10240',
            'fees' => 'required|array|min:1|max:2',
            'fees.*.type' => 'required|integer|in:0,1',
            'fees.*.fee' => 'exclude_if:fees.*.type,1|required|integer|min:0',
            'fees.*.fee' => 'exclude_if:fees.*.type,0|required|numeric|min:0|max:100',
        ]);

        $fees = $this->formatInputFees($request->fees);

        $picture = $bank->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/banks/', 3/1);

            if(File::exists(public_path($bank->picture))){
                File::delete(public_path($bank->picture));
            }
        }

        $bank->update(
            $request->only(['name', 'code', 'description']) +
            [
                'picture' => $picture,
                'fees' => $fees,
            ]
        );

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Bank $bank): JsonResponse
    {
        if(File::exists(public_path($bank->picture))){
            File::delete(public_path($bank->picture));
        }

        $bank->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }

    private function formatInputFees(array $fees = []) : array {
        return collect($fees)
            ->map(function($fee, $key){
                $type = (int) $fee['type'];
                return (object) [
                    'type' => $type,
                    'fee' => ($type == 1) ? (float) $fee['fee'] : (int) $fee['fee'],
                ];
            })
            ->all();
    }
}
