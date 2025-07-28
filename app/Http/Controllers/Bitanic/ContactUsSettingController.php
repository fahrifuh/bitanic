<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\ContactUsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUsSettingController extends Controller
{
    public function index()
    {
        $data['data'] = ContactUsSetting::first();

        return view('bitanic.contact-us-setting.index', $data);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'linkedin_link' => 'required|string|max:255',
            'ig_link' => 'required|string|max:255',
            'facebook_link' => 'required|string|max:255',
            'mitra_link' => 'required|url',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $data = ContactUsSetting::first();

        $data->update($request->only([
            'email',
            'phone_number',
            'linkedin_link',
            'ig_link',
            'facebook_link',
            'mitra_link',
        ])+[
            'address' => $request->alamat
        ]);

        return back()->with('success', 'Berhasil');
    }
}
