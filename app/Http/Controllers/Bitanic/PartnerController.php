<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $partners = $partners->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('partner_type', 'LIKE', '%' . $search . '%')
                    ->orWhere('contact', $search);
            });
        }

        if (request()->query('tanggal_bergabung') && validateDate(request()->query('tanggal_bergabung'), 'Y-m-d')) {
            $tanggal_bergabung = request()->query('tanggal_bergabung');
            $partners = $partners->where(function ($query) use ($tanggal_bergabung) {
                $query->whereDate('date_joining', $tanggal_bergabung);
            });
        }

        if (request()->query('tanggal_kontrak') && validateDate(request()->query('tanggal_kontrak'), 'Y-m-d')) {
            $tanggal_kontrak = request()->query('tanggal_kontrak');
            $partners = $partners->where(function ($query) use ($tanggal_kontrak) {
                $query->whereDate('contract_date', $tanggal_kontrak);
            });
        }

        $data['data'] = $partners->select(['id', 'contact', 'picture', 'name', 'partner_type', 'date_joining', 'contract_date'])
            ->latest()
            ->paginate(10)->withQueryString();

        return view('bitanic.partner.index', $data);
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
            'contact' => 'required|string|max:255',
            'date_joining' => 'required|date',
            'partner_type' => 'required|string|max:255',
            'contract_date' => 'required|date',
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

        $foto = image_intervention($request->file('picture'), 'bitanic-photo/partners/');
        
        $partner = Partner::create(
            $request->except('created_at', 'updated_at', 'id', 'picture')  + [
                'picture' => $foto,
            ],
        );

        session()->flash('success', 'Berhasil disimpan');

        activity()
            ->performedOn($partner)
            ->withProperties(
                collect($partner)
                    ->except(['id', 'created_at', 'updated_at']),
            )
            ->event('created')
            ->log('created');

        return response()->json([
            'message' => 'Berhasil!'
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
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data mitra tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'date_joining' => 'required|date',
            'partner_type' => 'required|string|max:255',
            'contract_date' => 'required|date',
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
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/partners/');
            // dd($request->file('picture'), $foto);

            if (File::exists(public_path($partner->picture))) {
                File::delete(public_path($partner->picture));
            }

            $partner->picture = $foto;
            $partner->save();
            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        $original = $partner->getOriginal();

        $partner->update($request->except('created_at', 'updated_at', 'picture', 'id'));

        session()->flash('success', 'Berhasil disimpan');

        $changes = collect($partner->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($partner)
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
            'message' => 'Berhasil!'
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
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data mitra tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (File::exists(public_path($partner->picture))) {
            File::delete(public_path($partner->picture));
        }

        activity()
            ->performedOn($partner)
            ->withProperties(
                collect($partner)
                    ->except(['id', 'created_at', 'updated_at'])
            )
            ->event('deleted')
            ->log('deleted');

        $partner->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
            'message' => 'Berhasil!'
        ]);
    }
}
