<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $faqs = Faq::query()
            ->latest()
            ->paginate(10);

        return view('bitanic.landing-setting.faq.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string|max:1000',
        ]);

        Faq::create($request->only(['question', 'answer']));

        return redirect()
            ->route('bitanic.faq.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Faq  $faq
     */
    public function edit(Faq $faq): View
    {
        return view('bitanic.landing-setting.faq.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faq  $faq
     */
    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string|max:1000',
        ]);

        $faq->update($request->only(['question', 'answer']));

        return redirect()
            ->route('bitanic.faq.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
