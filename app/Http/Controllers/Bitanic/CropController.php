<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $crop = Crop::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $crop = $crop->where(function($query)use($search){
                $query->where('crop_name', 'LIKE', '%'.$search.'%');
            });
        }

        if (request()->query('jenis') && in_array(request()->query('jenis'), ['sayur', 'buah'])) {
            $jenis = request()->query('jenis');
            $crop = $crop->where(function($query)use($jenis){
                $query->where('type', $jenis);
            });
        }

        if (request()->query('musim') && in_array(request()->query('musim'), ['hujan', 'kemarau'])) {
            $musim = request()->query('musim');
            $crop = $crop->where(function($query)use($musim){
                $query->where('season', $musim);
            });
        }

        $data['data'] = $crop->orderBy('crop_name')->paginate(10)->withQueryString();

        return view('bitanic.crop.index', $data);
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
            'crop_name' => 'required|string|max:255',
            'type' => 'required|in:sayur,buah',
            'price' => 'required|numeric|min:0',
            'price_description' => 'required|string|max:250',
            'season' => 'required|in:hujan,kemarau',
            'optimum_temperature' => 'required|numeric',
            'minimum_temperature' => 'required|numeric',
            'maximum_temperature' => 'required|numeric',
            'optimum_moisture' => 'required|integer|min:0|max:100',
            'minimum_moisture' => 'required|integer|min:0|max:100',
            'maximum_moisture' => 'required|integer|min:0|max:100',
            'altitude' => 'required|integer',
            'description' => 'required',
            'picture' => 'required|image|mimes:jpg,png|max:2048',
            'target_ph' => 'required|numeric',
            'target_persen_corganik' => 'required|numeric',
            'frekuensi_siram' => 'required|integer',
            'n_kg_ha' => 'required|integer',
            'sangat_rendah_p2o5' => 'required|integer',
            'rendah_p2o5' => 'required|integer',
            'sedang_p2o5' => 'required|integer',
            'tinggi_p2o5' => 'required|integer',
            'sangat_tinggi_p2o5' => 'required|integer',
            'sangat_rendah_k2o' => 'required|integer',
            'rendah_k2o' => 'required|integer',
            'sedang_k2o' => 'required|integer',
            'tinggi_k2o' => 'required|integer',
            'sangat_tinggi_k2o' => 'required|integer',
            'catatan' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/crops/');

        $crop = Crop::create(
            $request->only([
                    'crop_name',
                    'type',
                    'season',
                    'optimum_temperature',
                    'minimum_temperature',
                    'maximum_temperature',
                    'altitude',
                    'description',
                    'price',
                    'price_description',
                    'frekuensi_siram',
                    'target_ph',
                    'target_persen_corganik',
                    'n_kg_ha',
                    'catatan'
                ]) + [
                'picture' => $foto,
                'sangat_rendah_p2o5' => $request->sangat_rendah_p2o5 ?? 0,
                'rendah_p2o5' => $request->rendah_p2o5 ?? 0,
                'sedang_p2o5' => $request->sedang_p2o5 ?? 0,
                'tinggi_p2o5' => $request->tinggi_p2o5 ?? 0,
                'sangat_tinggi_p2o5' => $request->sangat_tinggi_p2o5 ?? 0,
                'sangat_rendah_k2o' => $request->sangat_rendah_k2o ?? 0,
                'rendah_k2o' => $request->rendah_k2o ?? 0,
                'sedang_k2o' => $request->sedang_k2o ?? 0,
                'tinggi_k2o' => $request->tinggi_k2o ?? 0,
                'sangat_tinggi_k2o' => $request->sangat_tinggi_k2o ?? 0,
                'moisture' => [
                    "maximum" => (int) $request->maximum_moisture,
                    "minimum" => (int) $request->minimum_moisture,
                    "optimum" => (int) $request->optimum_moisture
                ]
            ],
        );

        activity()
            ->performedOn($crop)
            ->withProperties($request->only(['crop_name', 'type', 'season', 'optimum_temperature', 'minimum_temperature', 'maximum_temperature', 'altitude', 'description', 'price', 'price_description']))
            ->event('created')
            ->log('created');

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
    public function show(Crop $crop)
    {
        return response()->json($crop);
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
        $data = Crop::find($id);

        if (!$data) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $v = Validator::make($request->all(), [
            'crop_name' => 'required|string|max:255',
            'type' => 'required|in:sayur,buah',
            'price' => 'required|numeric|min:0',
            'price_description' => 'required|string|max:250',
            'season' => 'required|in:hujan,kemarau',
            'optimum_temperature' => 'required|numeric',
            'minimum_temperature' => 'required|numeric',
            'maximum_temperature' => 'required|numeric',
            'optimum_moisture' => 'required|integer|min:0|max:100',
            'minimum_moisture' => 'required|integer|min:0|max:100',
            'maximum_moisture' => 'required|integer|min:0|max:100',
            'altitude' => 'required|integer',
            'description' => 'required',
            'picture' => 'nullable|image|mimes:jpg,png|max:2048',
            'target_ph' => 'required|numeric',
            'target_persen_corganik' => 'required|numeric',
            'frekuensi_siram' => 'required|integer',
            'n_kg_ha' => 'required|integer',
            'sangat_rendah_p2o5' => 'required|integer',
            'rendah_p2o5' => 'required|integer',
            'sedang_p2o5' => 'required|integer',
            'tinggi_p2o5' => 'required|integer',
            'sangat_tinggi_p2o5' => 'required|integer',
            'sangat_rendah_k2o' => 'required|integer',
            'rendah_k2o' => 'required|integer',
            'sedang_k2o' => 'required|integer',
            'tinggi_k2o' => 'required|integer',
            'sangat_tinggi_k2o' => 'required|integer',
            'catatan' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $picture_new = [];
        $picture_old = [];

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/crops/');

            if (\File::exists(public_path($data->picture))) {
                \File::delete(public_path($data->picture));
            }

            $data->picture = $foto;
            $data->save();

            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        $original = $data->getOriginal();

        $data->update($request->only([
            'crop_name',
            'type',
            'season',
            'optimum_temperature',
            'minimum_temperature',
            'maximum_temperature',
            'altitude',
            'description',
            'price',
            'price_description',
            'frekuensi_siram',
            'target_ph',
            'target_persen_corganik',
            'n_kg_ha',
            'catatan'
        ]) + [
            'sangat_rendah_p2o5' => $request->sangat_rendah_p2o5 ?? 0,
            'rendah_p2o5' => $request->rendah_p2o5 ?? 0,
            'sedang_p2o5' => $request->sedang_p2o5 ?? 0,
            'tinggi_p2o5' => $request->tinggi_p2o5 ?? 0,
            'sangat_tinggi_p2o5' => $request->sangat_tinggi_p2o5 ?? 0,
            'sangat_rendah_k2o' => $request->sangat_rendah_k2o ?? 0,
            'rendah_k2o' => $request->rendah_k2o ?? 0,
            'sedang_k2o' => $request->sedang_k2o ?? 0,
            'tinggi_k2o' => $request->tinggi_k2o ?? 0,
            'sangat_tinggi_k2o' => $request->sangat_tinggi_k2o ?? 0,
            'moisture' => [
                "maximum" => (int) $request->maximum_moisture,
                "minimum" => (int) $request->minimum_moisture,
                "optimum" => (int) $request->optimum_moisture
            ]
        ]);

        $changes = collect($data->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($data)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['updated_at', 'picture'])
                                ->merge($picture_old)
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['updated_at', 'picture'])
                                ->merge($picture_new)
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');

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
        $data = Crop::find($id);

        if (!$data) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (\File::exists(public_path($data->picture))) {
            \File::delete(public_path($data->picture));
        }

        activity()
            ->performedOn($data)
            ->withProperties(['name', $data->crop_name])
            ->event('deleted')
            ->log('deleted');

        $data->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
