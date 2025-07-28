<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function listArtikel()
    {
        $data['artikel_terbaru'] = Article::query()
            ->select(['id', 'title', 'picture', 'type', 'date', 'source', 'description'])
            ->whereIn('type', ['buah', 'sayuran', 'umum'])
            ->withCount('article_view', 'article_like')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        $data['semua_artikel'] = Article::query()
            ->select(['id', 'title', 'picture', 'type', 'date', 'source', 'description'])
            ->whereIn('type', ['buah', 'sayuran', 'umum'])
            ->withCount('article_view', 'article_like')
            ->orderBy('date', 'desc')
            ->get();

        $data['message'] = "List data artikel";
        $data['status'] = 200;

        return response()->json($data, 200);
    }

    public function artikelView($id)
    {
        $data = Article::find($id);

        if (!$data) {
            return response()->json([
                'message' => "Artikel tidak ditemukan!",
                'status' => 404
            ], 404);
        }

        $user_id = auth()->user()->id;

        $check = Article::whereHas('article_view', function($q)use($user_id){
            $q->where('user_id', $user_id);
        })->where('id', $id)->first();

        if (!$check) {
            DB::table('article_view')->insert([
                'article_id' => $id,
                'user_id' => $user_id,
                'created_at' => now('Asia/Jakarta')
            ]);

            $data->count_view += 1;
            $data->save();
        }

        return response()->json([
            'message' => "Success",
            'count_view' => $data->count_view,
            'status' => 200
        ], 200);
    }

    public function artikelLike($id)
    {
        $data = Article::find($id);

        if (!$data) {
            return response()->json([
                'message' => "Artikel tidak ditemukan!",
                'status' => 404
            ], 404);
        }

        $user_id = auth()->user()->id;

        $check = Article::whereHas('article_like', function($q)use($user_id){
            $q->where('user_id', $user_id);
        })->where('id', $id)->first();

        if (!$check) {
            DB::table('article_like')->insert([
                'article_id' => $id,
                'user_id' => $user_id,
                'created_at' => now('Asia/Jakarta')
            ]);

            $data->count_like += 1;
            $data->save();
        }

        return response()->json([
            'message' => "Success",
            'count_like' => $data->count_like,
            'status' => 200
        ], 200);
    }

    public function deleteLike($id)
    {
        $data = Article::find($id);

        if (!$data) {
            return response()->json([
                'message' => "Artikel tidak ditemukan!",
                'status' => 404
            ], 404);
        }

        $user_id = auth()->user()->id;

        $check = Article::whereHas('article_like', function($q)use($user_id){
            $q->where('user_id', $user_id);
        })->where('id', $id)->first();

        if ($check) {
            DB::table('article_like')->where([
                ['user_id', $user_id],
                ['article_id', $id]
            ])->delete();

            $data->count_like -= 1;
            $data->save();
        }

        return response()->json([
            'message' => "Success",
            'count_like' => $data->count_like,
            'status' => 200
        ], 200);
    }
}
