<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PractitionerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $practitioners = Practitioner::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $practitioners = $practitioners->where(function($query)use($search){
                $query->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhere('practitioner_field', 'LIKE', '%'.$search.'%')
                    ->orWhere('institution', 'LIKE', '%'.$search.'%');
            });
        }

        $data['data'] = $practitioners->select(['id', 'name', 'address', 'practitioner_field', 'institution', 'picture']
            )->paginate(10)->withQueryString();

        return view('bitanic.practitioner.index', $data);
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
            'name' => 'required|string|max:255',
            'practitioner_field' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'address' => 'required|string|max:500',
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

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/practitioners/');

        $practitioner = Practitioner::create(
            $request->only(['name', 'practitioner_field', 'institution', 'address']) + [
                'picture' => $foto,
            ],
        );

        session()->flash('success', 'Berhasil disimpan');

        activity()
            ->performedOn($practitioner)
            ->withProperties(
                collect($practitioner)
                    ->except(['id', 'picture', 'created_at', 'updated_at']),
            )
            ->event('created')
            ->log('created');

        return response()->json([
            'message' => 'Berhasil!',
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
        $practitioner = Practitioner::find($id);

        if (!$practitioner) {
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
            'name' => 'required|string|max:255',
            'practitioner_field' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'address' => 'required|string|max:500',
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

        $picture_new = [];
        $picture_old = [];

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/practitioners/');

            if (\File::exists(public_path($practitioner->picture))) {
                \File::delete(public_path($practitioner->picture));
            }

            $practitioner->picture = $foto;
            $practitioner->save();
            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        $original = $practitioner->getOriginal();

        $practitioner->update($request->only(['name', 'practitioner_field', 'institution', 'address']));

        session()->flash('success', 'Berhasil disimpan');

        $changes = collect($practitioner->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($practitioner)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['picture', 'updated_at'])
                                ->merge($picture_old)
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['picture', 'updated_at'])
                                ->merge($picture_new)
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');

        return response()->json([
            'message' => 'Berhasil!',
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
        $practitioner = Practitioner::find($id);

        if (!$practitioner) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        activity()
            ->performedOn($practitioner)
            ->withProperties(
                collect($practitioner)
                    ->only(['name', 'practitioner_field', 'institution'])
            )
            ->event('deleted')
            ->log('deleted');

        $practitioner->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
            'message' => 'Berhasil!',
        ]);
    }
}
