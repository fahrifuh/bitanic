<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $galleries = Gallery::query()
            ->latest()
            ->paginate(9);

        return view('bitanic.landing-setting.gallery.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'picture'       => 'required|image|mimes:jpg,png,svg|max:2048',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);
        
        $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/gallery/', 1/1);

        Gallery::create($request->only(['title', 'description']) + ['picture' => $picture]);

        return redirect()
            ->route('bitanic.gallery.index')
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        return view('bitanic.landing-setting.gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     */
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'picture'       => 'nullable|image|mimes:jpg,png|max:2048',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);

        $picture = $gallery->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/gallery/', 1/1);

            deleteImage($gallery->picture);
        }

        $gallery->update($request->only(['title', 'description']) + ['picture' => $picture]);
        
        return redirect()
            ->route('bitanic.gallery.index')
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     */
    public function destroy(Gallery $gallery): JsonResponse
    {
        deleteImage($gallery->picture);

        $gallery->delete();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
