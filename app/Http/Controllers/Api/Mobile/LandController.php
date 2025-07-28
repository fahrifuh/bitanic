<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lands = Land::query()
            ->select([
                'id',
                'farmer_id',
                'name',
                'area',
                'address',
                'image',
                'latitude',
                'longitude',
                'altitude',
                'polygon',
                'color',
            ])
            ->with([
                'gardens:id,land_id,polygon,color,name'
            ])
            ->where('farmer_id', auth()->user()->farmer->id);

        $search = $request->query('search', null);

        if ($search && is_string($search)) {
            $search = '%' . $search . '%';
            $lands = $lands->where(function($query)use($search){
                $query->where('name', 'LIKE', $search)
                    ->orWhere('address', 'LIKE', $search);
            });
        }

        $lands = $lands
            ->orderBy('created_at')
            ->simplePaginate(10);

        return response()->json([
            'lands' => $lands,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->polygon || (!is_array($request->polygon) && !is_string($request->polygon))) {
            return response()->json([
                'messages' => [
                    'polygon' => [
                        'Error data polygon.'
                    ]
                ],
                'status' => 400
            ], 400);
        }
        if (is_string($request->polygon)) {
            $request['polygon'] = json_decode($request->polygon);
        }

        $v = Validator::make($request->all(), [
            'polygon.*.*' => ['required', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'polygon.*' => ['required', 'array'],
            'polygon' => ['required', 'array'],
            'latitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude' => ['required', 'numeric'],
            'area' => ['required', 'numeric', 'min:0'],
            'name' => ['required', 'string', 'max:255'],
            'color'     => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'address'           => 'required|string|max:1000',
            'image'           => 'required|image|mimes:jpg,png,jpeg|max:20480',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $image = image_intervention($request->file('image'), 'bitanic-photo/lands/', 4 / 3);

        $farmer = auth()->user()->farmer;

        $land = Land::create($request->only([
            'latitude',
            'longitude',
            'altitude',
            'area',
            'name',
            'address',
        ]) + [
            'image' => $image,
            'polygon' => $request->polygon,
            'farmer_id' => $farmer->id,
            'color' => substr($request->color, 1),
        ]);


        activity()
            ->performedOn($land)
            ->withProperties(
                collect($land)
                    ->except(['id', 'image', 'created_at', 'updated_at'])
                    ->merge([
                        'farmer' => $land->farmer_id == null ? null : $farmer->full_name
                    ]),
            )
            ->event('created')
            ->log('created');

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $land = Land::query()
            ->with([
                'rsc_telemetries',
                'gardens:id,land_id,picture,name,polygon,color,harvest_status,device_id',
                'gardens.device:id,device_series',
                'gardens.currentCommodity:id,garden_id,crop_id',
                'gardens.currentCommodity.crop:id,crop_name,picture',
            ])
            ->where('farmer_id', auth()->user()->farmer->id)
            ->find($id);

        if (!$land) {
            return response()->json([
                'messages' => [
                    'errors' => ['Data tidak ditemukan!']
                ],
                'status' => 404
            ], 404);
        }

        return response()->json([
            'land' => $land,
            'status' => 200
        ]);
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
        $land = Land::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->find($id);

        if (!$land) {
            return response()->json([
                'messages' => [
                    'errors' => ['Data tidak ditemukan!']
                ],
                'status' => 404
            ], 404);
        }

        if (!$request->polygon || (!is_array($request->polygon) && !is_string($request->polygon))) {
            return response()->json([
                'messages' => [
                    'polygon' => [
                        'Error data polygon.'
                    ]
                ],
                'status' => 400
            ], 400);
        }

        if ($request->polygon && is_string($request->polygon)) {
            $request['polygon'] = json_decode($request->polygon);
        }

        $v = Validator::make($request->all(), [
            'polygon.*.*' => ['required', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'polygon.*' => ['required', 'array'],
            'polygon' => ['required', 'array'],
            'latitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude' => ['required', 'numeric'],
            'area' => ['required', 'numeric', 'min:0'],
            'name' => ['required', 'string', 'max:255'],
            'address'           => 'required|string|max:1000',
            'image'           => 'nullable|image|mimes:jpg,png,jpeg|max:20048',
            'color'     => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $picture_new = [];
        $picture_old = [];
        $image = $land->image;

        if ($request->file('image')) {
            $image = image_intervention($request->file('image'), 'bitanic-photo/lands/', 4 / 3);

            if (File::exists(public_path($land->image))) {
                File::delete(public_path($land->image));
            }

            $picture_new = ['image' => 'Updated'];
            $picture_old = ['image' => 'Old'];
        }

        $original = $land->getOriginal();

        $land->update($request->only([
            'latitude',
            'longitude',
            'altitude',
            'area',
            'name',
            'address',
        ]) + [
            'image' => $image,
            'polygon' => $request->polygon,
            'color' => substr($request->color, 1),
        ]);

        $changes = collect($land->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($land)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['image', 'updated_at'])
                                ->merge($picture_old)
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['image', 'updated_at'])
                                ->merge($picture_new)
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');

        return response()->json([
            'message' => 'Berhasil disimpan',
            'a' => substr($request->color, 1),
            'status' => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $land = Land::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->find($id);

        if (!$land) {
            return response()->json([
                'messages' => [
                    'errors' => ['Data tidak ditemukan!']
                ],
                'status' => 404
            ], 404);
        }

        if ($land->image && File::exists(public_path($land->image))) {
            File::delete(public_path($land->image));
        }

        activity()
            ->performedOn($land)
            ->withProperties(['name', $land->name])
            ->event('deleted')
            ->log('deleted');

        $land->delete();

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ]);
    }

    public function updatePolygon(Request $request, $id)
    {
        $land = Land::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->find($id);

        if (!$land) {
            return response()->json([
                'messages' => [
                    'errors' => ['Data tidak ditemukan!']
                ],
                'status' => 404
            ], 404);
        }

        [$error_status, $error_messages] = $this->polygonValidation($request->polygon);

        if ($error_status) {
            return response()->json([
                'messages' => [
                    'polygon' => $error_messages
                ],
                'status' => 400
            ], 400);
        }

        if ($request->polygon && is_string($request->polygon)) {
            $request['polygon'] = json_decode($request->polygon);
        }

        $v = Validator::make($request->all(),[
            'latitude'    => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'    => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude'    => 'required|numeric',
            'polygon.*.*' => ['required', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'polygon.*' => ['required', 'array'],
            'polygon' => ['required', 'array'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $land->update($request->only(['polygon','latitude','longitude','altitude']));

        return response()->json([
            'message' => "Berhasil disimpan",
            'status' => 200
        ], 200);
    }

    public function polygonValidation($polygon)
    {
        if (!$polygon) {
            return [true, [
                'Harap isi data polygon!'
            ]];
        }
        if (!is_array($polygon) && !is_string($polygon)) {
            return [
                true,
                [
                    'Format data polygon tidak sesuai!'
                ]
            ];
        }

        return [
            false,
            null
        ];
    }
}
