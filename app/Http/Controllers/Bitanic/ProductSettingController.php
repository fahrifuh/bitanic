<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\ProductSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSettingController extends Controller
{
    public function index()
    {
        $data['data'] = ProductSetting::firstOrFail();
        return view('bitanic.product-setting.index', $data);
    }

    public function update(Request $request)
    {
        $data = ProductSetting::firstOrFail();

        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:50',
            'sub' => 'required|string|max:50',
            'description' => 'required|string|max:700'
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $data->update($request->only(['title', 'sub', 'description']));

        return back()->with('success', 'Berhasil');
    }
}
