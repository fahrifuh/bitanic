<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\Testimony;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TestimonyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $testimonies = Testimony::query()
            ->latest()
            ->paginate(9);

        return view('bitanic.landing-setting.testimony.index', compact('testimonies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.testimony.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'picture'   => 'required|image|mimes:jpg,png,svg|max:2048',
            'name'     => 'required|string|max:255',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'required|string|max:255',
        ]);
        
        $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/testimony/', 1/1);

        Testimony::create($request->only(['name', 'rating', 'comment']) + ['picture' => $picture]);

        return redirect()
            ->route('bitanic.testimony.index')
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Testimony  $testimony
     */
    public function show(Testimony $testimony): View
    {
        return view('bitanic.landing-setting.testimony.show', compact('testimony'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Testimony  $testimony
     */
    public function edit(Testimony $testimony): View
    {
        return view('bitanic.landing-setting.testimony.edit', compact('testimony'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimony  $testimony
     */
    public function update(Request $request, Testimony $testimony): RedirectResponse
    {
        $request->validate([
            'picture'   => 'nullable|image|mimes:jpg,png,svg|max:2048',
            'name'     => 'required|string|max:255',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'required|string|max:255',
        ]);

        $picture = $testimony->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/testimony/', 1/1);

            deleteImage($testimony->picture);
        }

        $testimony->update($request->only(['name', 'rating', 'comment']) + ['picture' => $picture]);

        return redirect()
            ->route('bitanic.testimony.index')
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Testimony  $testimony
     * @return \Illuminate\Http\Response
     */
    public function destroy(Testimony $testimony)
    {
        deleteImage($testimony->picture);

        $testimony->delete();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
