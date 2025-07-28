<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $careers = Career::query()
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.landing-setting.career.index', compact('careers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.career.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'is_looking' => 'sometimes|required|accepted',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'work_hour' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'requirements' => 'required|array',
            'requirements.*' => 'string|max:255',
        ]);

        Career::create($request->only([
            'position',
            'department',
            'work_hour',
            'location',
            'description',
            'requirements',
        ]) + [
            'is_looking' => $request->get('is_looking', 0)
        ]);

        return redirect()
            ->route('bitanic.career.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Career  $career
     */
    public function show(Career $career): View
    {
        return view('bitanic.landing-setting.career.show', compact('career'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Career  $career
     */
    public function edit(Career $career): View
    {
        return view('bitanic.landing-setting.career.edit', compact('career'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Career  $career
     */
    public function update(Request $request, Career $career): RedirectResponse
    {
        $request->validate([
            'is_looking' => 'sometimes|required|accepted',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'work_hour' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'requirements' => 'required|array',
            'requirements.*' => 'string|max:255',
        ]);

        $career->update($request->only([
            'position',
            'department',
            'work_hour',
            'location',
            'description',
            'requirements',
        ]) + [
            'is_looking' => $request->get('is_looking', 0)
        ]);

        return redirect()
            ->back()
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Career  $career
     */
    public function destroy(Career $career): JsonResponse | RedirectResponse
    {
        $career->delete();

        $message = 'Berhasil dihapus';

        session()->flash('success', $message);

        if (request()->wantsJson()) {
            return response()
                ->json([
                    'message' => $message
                ]);
        }

        return redirect()
            ->route('bitanic.career.index');
    }

    public function updateStatus(Career $career) : JsonResponse {
        $career->is_looking = $career->is_looking ? 0 : 1;
        $career->save();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
