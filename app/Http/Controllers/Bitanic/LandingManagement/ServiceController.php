<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $services = Service::query()
            ->latest()
            ->paginate(10);

        return view('bitanic.landing-setting.service.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.service.create');
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
            'picture'       => 'required|image|mimes:jpg,png,svg|max:2048',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);
        
        $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/service/', 1/1);

        Service::create(
            $request->only(['title', 'description']) + 
            ['icon' => $picture]
        );

        return redirect()
            ->route('bitanic.service.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service): View
    {
        return view('bitanic.landing-setting.service.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service): View
    {
        return view('bitanic.landing-setting.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'picture'       => 'nullable|image|mimes:jpg,png,svg|max:2048',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);

        $picture = $service->icon;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/service/', 1/1);

            if (File::exists(public_path($service->icon))) {
                File::delete(public_path($service->icon));
            }
        }

        $service->update($request->only(['title', 'description']) + ['picture' => $picture]);

        return redirect()
            ->route('bitanic.service.show', $service->id)
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        if (File::exists(public_path($service->icon))) {
            File::delete(public_path($service->icon));
        }

        $service->delete();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
