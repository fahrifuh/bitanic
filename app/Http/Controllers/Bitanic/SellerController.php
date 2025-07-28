<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $sellers = $sellers->where(function($query)use($search){
                $query->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhere('bussiness_segment', 'LIKE', '%'.$search.'%');
            });
        }

        if (request()->query('tanggal_bergabung') && validateDate(request()->query('tanggal_bergabung'), 'Y-m-d')) {
            $tanggal_bergabung = request()->query('tanggal_bergabung');
            $sellers = $sellers->where(function($query)use($tanggal_bergabung){
                $query->whereDate('date_joining', $tanggal_bergabung);
            });
        }

        $data['data'] = $sellers->select(['id', 'name', 'date_joining', 'bussiness_segment', 'address', 'picture'])
            ->paginate(10)->withQueryString();

        return view('bitanic.seller.index', $data);
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
            'date_joining' => 'required|date',
            'bussiness_segment' => 'required|string|max:255',
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

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/sellers/');

        $seller = Seller::create(
            $request->only(['name', 'date_joining', 'bussiness_segment', 'address']) + [
                'picture' => $foto,
            ],
        );

        session()->flash('success', 'Berhasil disimpan');

        activity()
            ->performedOn($seller)
            ->withProperties(
                collect($seller)
                    ->except(['id', 'created_at', 'updated_at', 'picture']),
            )
            ->event('created')
            ->log('created');

        return response()->json([
            'message' => 'Berhasil',
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
        $seller = Seller::find($id);

        if (!$seller) {
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
            'date_joining' => 'required|date',
            'bussiness_segment' => 'required|string|max:255',
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
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/sellers/');

            if (\File::exists(public_path($seller->picture))) {
                \File::delete(public_path($seller->picture));
            }

            $seller->picture = $foto;
            $seller->save();
            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        $original = $seller->getOriginal();

        $seller->update($request->except(['created_at', 'updated_at', 'picture', 'id']));

        session()->flash('success', 'Berhasil disimpan');

        $changes = collect($seller->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($seller)
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
            'message' => 'Berhasil',
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
        $seller = Seller::find($id);

        if (!$seller) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (\File::exists(public_path($seller->picture))) {
            \File::delete(public_path($seller->picture));
        }

        activity()
            ->performedOn($seller)
            ->withProperties(
                collect($seller)
                    ->only(['name', 'date_joining', 'bussiness_segment'])
            )
            ->event('deleted')
            ->log('deleted');

        $seller->delete();

        session()->flash('success', 'Berhasil diihapus');

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }
}
