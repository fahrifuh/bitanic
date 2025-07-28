<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\AboutOurStarup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AboutOurStartup extends Controller
{
    public function index()
    {
        $data['data'] = AboutOurStarup::firstOrFail();

        return view('bitanic.about-our-startup.index', $data);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'description' => 'required|string|max:700',
            'picture'           => 'nullable|array',
            'picture.*'           => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $data = AboutOurStarup::first();

        $data->update($request->only([
            'description'
        ]));

        return back()->with('success', 'Berhasil');
    }

    public function createEventImages() : View {
        return view('bitanic.about-our-startup.add-image');
    }

    public function storeEventImages(Request $request)
    {
        $request->validate([
            'picture'           => 'nullable|array',
            'picture.*'           => 'nullable|image|mimes:jpg,png|max:10240'
        ]);

        $new_images = [];

        if ($request->picture) {
            foreach ($request->picture as $picture) {
                array_push($new_images, image_intervention($picture, 'bitanic-photo/about-starup/', (3 / 2)));
            }
        }

        $data = AboutOurStarup::first();

        $images = $data->event_images ?? [];

        $merge_images = [...$images, ...$new_images];

        $data->update([
            'event_images' => $merge_images
        ]);

        return redirect()->route('bitanic.about-our-startup-setting.index');
    }

    public function deleteImages() : View {
        $data['about_our_starup'] = AboutOurStarup::select('event_images')->firstOrFail();

        return view('bitanic.about-our-startup.delete-images', $data);
    }

    public function destroyImages(Request $request) {
        $request->validate([
            'images'           => 'required|array',
            'images.*'           => 'required|string'
        ]);

        $about_our_starup = AboutOurStarup::firstOrFail();

        $collection = collect($about_our_starup->event_images);

        $delete_images = $collection->intersect($request->images);
        $update_images = $collection->diff($request->images);

        if ($delete_images->count() < 1) {
            return back()->withErrors([
                "messages" => ["Tidak ada gambar yang dipilih"]
            ]);
        }

        $checked_images = $delete_images->filter(function ($delete_image, $key) {
            if (File::exists(public_path($delete_image))) {
                return public_path($delete_image);
            }
        });

        if ($checked_images->count() > 0) {
            File::delete($checked_images->all());
        }

        $about_our_starup->update([
            'event_images' => $update_images->map(function($update_image, $key) {
                return $update_image;
            })->all()
        ]);

        return redirect()->route('bitanic.about-our-startup-setting.index')->with('success', 'Berhasil dihapus');
    }
}
