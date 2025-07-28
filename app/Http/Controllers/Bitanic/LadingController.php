<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\AboutOurStarup;
use App\Models\Article;
use App\Models\BitanicProduct;
use App\Models\ContactUsSetting;
use App\Models\ProductSetting;
use App\Models\Seller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LadingController extends Controller
{
    public function index()
    {
        $data['contact_us'] = ContactUsSetting::first();
        $data['about_our_startup'] = AboutOurStarup::first();
        $data['product'] = ProductSetting::first();
        $data['sellers'] = Seller::query()
            ->limit(10)
            ->get(['id', 'name', 'picture']);
        $data['articleTypes'] = Article::select('type')->whereNotIn('type', ['tentang_kami', 'visi_misi'])->distinct()->pluck('type');
        $data['articles'] = Article::latest()->get();
        $data['bitanicProducts'] = BitanicProduct::get();
        $data['latestHistory'] = Article::where('type', 'tentang_kami')->latest()->take(2)->get();
        $data['latestVisiMisi'] = Article::where('type', 'visi_misi')->latest()->first();
        $data['mitra'] = DB::table('sellers')->select(['name', 'picture'])->union(DB::table('partners')->select(['name', 'picture']))->union(DB::table('practitioners')->select(['name', 'picture']))->get();

        return view('bitanic.landing.index', $data);
    }

    public function mitra(): View
    {
        $data['contact_us'] = ContactUsSetting::first();
        $data['about_our_startup'] = AboutOurStarup::first();
        $data['product'] = ProductSetting::first();

        return view('bitanic.landing.mitra', $data);
    }
}
