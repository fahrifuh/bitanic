<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $articles = $articles->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            });
        }

        if (request()->query('tipe') && in_array(request()->query('tipe'), ['sayuran', 'buah', 'umum', 'tentang_kami', 'visi_misi'])) {
            $tipe = request()->query('tipe');
            $articles = $articles->where(function ($query) use ($tipe) {
                $query->where('type', $tipe);
            });
        }

        if (request()->query('tanggal') && validateDate(request()->query('tanggal'), 'Y-m-d')) {
            $tanggal = request()->query('tanggal');
            $articles = $articles->where(function ($query) use ($tanggal) {
                $query->whereDate('date', $tanggal);
            });
        }

        $data['data'] = $articles->select(['id', 'title', 'description', 'picture', 'type', 'date', 'source'])
            ->paginate(10)->withQueryString();

        return view('bitanic.article.index', $data);
    }

    public function create(): View
    {
        return view('bitanic.article.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'type' => 'required|in:sayuran,buah,umum,tentang_kami,visi_misi',
            'date' => 'required|date',
            'description' => 'required|string|max:5000',
            'picture' => 'required|image|mimes:jpg,png|max:10048',
            'writer' => 'required|string'
        ]);

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/articles/');

        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><i><strong><ul><ol><li><a><blockquote>';

        // Remove disallowed tags and attributes
        $description = strip_tags($request->description, $allowedTags);
        $description = preg_replace('/<(.*?)>/i', '<$1>', $description);

        Article::create(
            $request->only(['title', 'type', 'source', 'date', 'writer']) + [
                'picture' => $foto,
                'description' => $description
            ],
        );

        return redirect()->back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function edit(Article $article): View
    {
        return view('bitanic.article.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data iklan tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'type' => 'required|in:sayuran,buah,umum,tentang_kami,visi_misi',
            'date' => 'required|date',
            'description' => 'required|string|max:1800',
            'picture' => 'nullable|image|mimes:jpg,png|max:10048',
            'writer' => 'required|string'
        ]);

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/articles/');

            if (\File::exists(public_path($article->picture))) {
                \File::delete(public_path($article->picture));
            }

            $article->picture = $foto;
            $article->save();
        }

        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><i><strong><ul><ol><li><a><blockquote>';

        // Remove disallowed tags and attributes
        $description = strip_tags($request->description, $allowedTags);
        $description = preg_replace('/<(.*?)>/i', '<$1>', $description);

        $article->update(
            $request->only(['title', 'type', 'source', 'date', 'writer']) +
                [
                    'description' => $description
                ]
        );

        return redirect()->back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data iklan tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (\File::exists(public_path($article->picture))) {
            \File::delete(public_path($article->picture));
        }

        $article->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
