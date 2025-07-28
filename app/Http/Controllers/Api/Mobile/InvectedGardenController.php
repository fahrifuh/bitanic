<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\InvectedGarden;
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
    public function index($garden)
    {
        $user = auth()->user();

        $garden = Garden::query()
            ->with('invected.pest')
            ->find($garden);

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

        return response()->json([
            'invected' => $garden->invected,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $garden)
    {
        date_default_timezone_set('Asia/Jakarta');
        $user = auth()->user();

        $garden = Garden::find($garden);

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
            'picture'          => 'required|image|mimes:jpg,png|max:10548'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
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
    public function show($garden, $id)
    {
        $user = auth()->user();

        $invected = InvectedGarden::query()
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

        $collect = collect($invected);

        $new_collect = $collect->forget('garden');

        return response()->json([
            'invected' => $new_collect,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $garden, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $user = auth()->user();

        $invected = InvectedGarden::query()
            ->with('garden')
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
        
        if ($request->file('foto')) {
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
     * @param  int  $garden
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($garden, $id)
    {
        $user = auth()->user();

        $invected = InvectedGarden::query()
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
     * Remove the specified resource from storage.
     *
     * @param  int  $garden
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $garden, $id)
    {
        $user = auth()->user();

        $invected = InvectedGarden::query()
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
