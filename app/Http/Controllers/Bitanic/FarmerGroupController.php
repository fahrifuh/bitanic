<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $farmer_groups = FarmerGroup::query()
            ->with(['subdistrict.district.city.province'])
            ->withCount('farmers');
        
        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $farmer_groups = $farmer_groups->whereHas('subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        if (request()->query('search')) {
            $search = request()->query('search');
            $farmer_groups = $farmer_groups->where('name', 'LIKE', '%'.$search.'%');
        }

        if (request()->query('province') && is_numeric(request()->query('province'))) {
            $province = request()->query('province');
            $farmer_groups = $farmer_groups->whereHas('subdistrict.district.city', function($query)use($province){
                $query->where('province_id', $province);
            });
        }

        $data['data'] = $farmer_groups->paginate(10)->withQueryString();

        $provinces = Province::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $provinces = $provinces->whereHas('city', function($query){
                $query->where('id', auth()->user()->city_id);
            });
        }

        $data['provinces'] = $provinces->get(['id','prov_name'])->pluck('prov_name','id');

        return view('bitanic.farmer-group.index', $data);
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
            'name' => 'required|string|max:255|unique:farmer_groups,name',
            'address' => 'required|string|max:1000',
            'picture' => 'required|image|mimes:jpg,png|max:2048',
            'subdis_id' => 'required|exists:subdistricts,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                401,
            );
        }

        $pic = image_intervention($request->file('picture'), 'bitanic-photo/farmer-groups/');

        FarmerGroup::create($request->only(['name','address','subdis_id'])+[
            'picture' => $pic
        ]);

        return response()->json(
            [
                'message' => 'Berhasil disimpan',
            ],
            200,
        );
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
        $group = FarmerGroup::query()
            ->find($id);

        if (!$group) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Data group tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $user = auth()->user();
        
        if ($user->role == 'admin' && $user->city_id != null && $group->subdistrict->district->city_id != $user->city_id) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Anda tidak memiliki akses untuk menupdate data ini!'],
                    ],
                ],
                403,
            );
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:farmer_groups,name,'.$id,
            'address' => 'required|string|max:1000',
            'picture' => 'nullable|image|mimes:jpg,png|max:2048',
            'subdis_id' => 'required|exists:subdistricts,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                401,
            );
        }

        if ($request->file('picture')) {
            $pic = image_intervention($request->file('picture'), 'bitanic-photo/farmer-groups/');

            if (\File::exists(public_path($group->picture))) {
                \File::delete(public_path($group->picture));
            }

            $group->picture = $pic;
            $group->save();
        }

        $group->update($request->only(['name','address','subdis_id']));

        return response()->json(
            [
                'message' => 'Berhasil disimpan',
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
        $group = FarmerGroup::query()
            ->find($id);

        if (!$group) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Data group tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $user = auth()->user();
        
        if ($user->role == 'admin' && $user->city_id != null && $group->subdistrict->district->city_id != $user->city_id) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Anda tidak memiliki akses untuk menghapus data ini!'],
                    ],
                ],
                403,
            );
        }

        if (\File::exists(public_path($group->picture))) {
            \File::delete(public_path($group->picture));
        }

        $group->delete();

        return response()->json(
            [
                'message' => 'Berhasil dihapus',
            ],
            200,
        );
    }

    public function addFarmers(Request $request, $id)
    {
        $group = FarmerGroup::query()
            ->find($id);

        if (!$group) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Data group tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $farmers = json_decode($request->farmers);

        $v = Validator::make(['farmers' => $farmers], [
            'farmers' => 'required|array',
            'farmers.*' => 'exists:farmers,id',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        Farmer::query()
            ->whereIn('id', $farmers)
            ->update(['group_id' => $id]);

        return response()->json(
            [
                'message' => 'Berhasil disimpan',
            ],
            200,
        );
    }

    public function removeFarmers(Request $request, $id)
    {
        $group = FarmerGroup::query()
            ->find($id);

        if (!$group) {
            return response()->json(
                [
                    'messages' => (object) [
                        'errors' => ['Data group tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $farmers = json_decode($request->farmers);

        $v = Validator::make(['farmers' => $farmers], [
            'farmers' => 'required|array',
            'farmers.*' => 'exists:farmers,id',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        Farmer::query()
            ->whereIn('id', $farmers)
            ->update(['group_id' => null]);

        return response()->json(
            [
                'message' => 'Berhasil disimpan',
            ],
            200,
        );
    }
}
