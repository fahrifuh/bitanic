<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Article;
use App\Models\Crop;
use App\Models\Garden;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $user = auth()->user();
        $crop_id_from_garden = Garden::query()->select(['crop_id'])
            ->whereHas('land', function($land)use($user){
                $land->where('farmer_id', $user->farmer->id);
            })
            ->pluck('crop_id');

        $data['nama_akun'] = $user->name;
        $data['iklan'] = Advertisement::get();
        $data['tanaman'] = Crop::whereIn('id', $crop_id_from_garden)->limit(5)->get();
        $data['artikel'] = Article::query()
            ->select(['id', 'title', 'picture', 'type', 'date', 'source', 'description'])
            ->withCount('article_view', 'article_like')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => (object)$data,
            'message' => "Data home user",
            'status' => 200
        ], 200);
    }
}
