<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\InvectedGarden;
use App\Models\Pest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvectedGardenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $invectedGardens = InvectedGarden::query()->with([
            'garden:id,land_id',
            'garden.land:id,name',
            'pest:id,pest_type'
        ]);

        $gardens = Garden::query()
            ->with([
                'land:id,name'
            ]);

        if (request()->query('search')) {
            $search = request()->query('search');
            $invectedGardens = $invectedGardens
                ->where('pest_name', 'LIKE', '%'.$search.'%')
                ->orWhereHas('pest', function($query)use($search){
                    $query->where('pest_type', 'LIKE', '%'.$search.'%');
                })
                ->orWhereHas('garden.land', function($query)use($search){
                    $query->where('name', 'LIKE', '%'.$search.'%');
                });
        }

        if (auth()->user()->role == 'farmer') {
            $invectedGardens = $invectedGardens->whereHas('garden.land', function($query){
                $query->where('farmer_id', auth()->user()->farmer->id);
            });

            $gardens = $gardens->whereHas('land', function($query){
                $query->where('farmer_id', auth()->user()->farmer->id);
            });
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $invectedGardens = $invectedGardens->whereHas('garden.land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });

            $gardens = $gardens->whereHas('land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $invectedGardens = $invectedGardens
            ->orderBy('invected_date', 'desc')
            ->paginate(10);

        $data['data'] = $invectedGardens;
        $data['gardens'] = $gardens->get(['id', 'land_id', 'gardes_type']);

        return view('bitanic.invected-gardens.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'pest_id'    => [
                Rule::requiredIf(!$request->has('pest_name')),
                'exists:pests,id'
            ],
            'pest_name'  => [
                Rule::requiredIf(!$request->has('pest_id')),
                'string',
                'max:255'
            ],
            'garden_id'    => 'required',
            'invected_date'    => 'required|date',
            'picture'          => 'required|image|mimes:jpg,png|max:10548'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $garden = Garden::query()
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->whereHas('land', function($query){
                    $query->where('farmer_id', auth()->user()->farmer->id);
                });
            })
            ->find($request->garden_id);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/invected-gardens/');

        $request['pest_name'] = ($request->has('pest_id')) ? null : $request->pest_name;

        InvectedGarden::create($request->only(['pest_id', 'pest_name', 'invected_date'])+[
            'picture' => $picture,
            'garden_id' => $garden->id,
            'status' => 'unaddressed'
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ], 200);
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
        $user = auth()->user();

        $invected = InvectedGarden::query()
            ->with('garden')
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->whereHas('garden.land', function($query){
                    $query->where('farmer_id', auth()->user()->farmer->id);
                });
            })
            ->find($id);

        if (!$invected) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $invected->garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(),[
            'pest_id'    => [
                Rule::requiredIf(!$request->has('pest_name')),
                'exists:pests,id'
            ],
            'pest_name'  => [
                Rule::requiredIf(!$request->has('pest_id')),
                'string',
                'max:255'
            ],
            'invected_date'    => 'required|date',
            'picture'          => 'nullable|image|mimes:jpg,png|max:10548'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/invected-gardens/');

            if(\File::exists(public_path($invected->picture))){
                \File::delete(public_path($invected->picture));
            }

            $invected->picture = $picture;
            $invected->save();
        }


        $request['pest_name'] = ($request->has('pest_id')) ? null : $request->pest_name;
        $request['pest_id'] = (!$request->pest_name) ? $request->pest_id : null;

        $invected->update($request->only(['pest_id', 'pest_name', 'invected_date']));

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invected = InvectedGarden::query()
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->whereHas('garden.land', function($query){
                    $query->where('farmer_id', auth()->user()->farmer->id);
                });
            })
            ->find($id);

        if (!$invected) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $invected->garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $invected->delete();

        return response()->json([
            'message' => 'Berhasil dihapus.',
            'status' => 200
        ], 200);
    }

    /**
     * Update status of the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $invected = InvectedGarden::query()
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->whereHas('garden.land', function($query){
                    $query->where('farmer_id', auth()->user()->farmer->id);
                });
            })
            ->find($id);

        if (!$invected) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Data terinfeksi hama tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $invected->garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(),[
            'status'    => 'required|in:addressed,unaddressed'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $invected->status = $request->status;
        $invected->save();

        return response()->json([
            'message' => 'Status berhasil disimpan.',
            'status' => 200
        ], 200);
    }
}
