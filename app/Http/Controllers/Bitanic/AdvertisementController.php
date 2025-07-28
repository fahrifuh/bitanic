<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $advertisement = Advertisement::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $advertisement = $advertisement->where(function($query)use($search){
                $query->where('title', 'LIKE', '%'.$search.'%');
            });
        }

        if (request()->query('tanggal_dimulai') && validateDate(request()->query('tanggal_dimulai'), 'Y-m-d')) {
            $tanggal_dimulai = request()->query('tanggal_dimulai');
            $advertisement = $advertisement->where(function($query)use($tanggal_dimulai){
                $query->whereDate('ads_start', $tanggal_dimulai);
            });
        }

        $data['data'] = $advertisement->select(['id', 'title', 'picture', 'description', 'ads_start'])
            ->paginate(10)->withQueryString();

        return view('bitanic.advertisement.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'ads_start' => 'required|date',
            'description' => 'required|string|max:500',
            'picture' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/ads/');

        Advertisement::create(
            $request->only(['title', 'ads_start', 'description']) + [
                'picture' => $foto,
            ],
        );

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ad = Advertisement::find($id);

        if (!$ad) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data iklan tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'ads_start' => 'required|date',
            'description' => 'required|string|max:500',
            'picture' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/ads/');

            if (\File::exists(public_path($ad->picture))) {
                \File::delete(public_path($ad->picture));
            }

            $ad->picture = $foto;
            $ad->save();
        }

        $ad->update($request->only(['title', 'ads_start', 'description']));

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ad = Advertisement::find($id);

        if (!$ad) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data iklan tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (\File::exists(public_path($ad->picture))) {
            \File::delete(public_path($ad->picture));
        }

        $ad->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
