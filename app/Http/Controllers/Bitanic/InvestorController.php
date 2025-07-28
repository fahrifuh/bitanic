<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $investors = Investor::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $investors = $investors->where(function($query)use($search){
                $query->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhere('investment_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('agreement_number', 'LIKE', '%'.$search.'%');
            });
        }

        if (request()->query('tanggal_perjanjian') && validateDate(request()->query('tanggal_perjanjian'), 'Y-m-d')) {
            $tanggal_perjanjian = request()->query('tanggal_perjanjian');
            $investors = $investors->where(function($query)use($tanggal_perjanjian){
                $query->whereDate('agreement_date', now()->parse($tanggal_perjanjian)->format('Y-m-d'));
            });
        }

        $data['data'] = $investors->select(['id', 'name', 'investment_name', 'agreement_number', 'agreement_date'])
            ->paginate(10)->withQueryString();

        return view('bitanic.investor.index', $data);
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
            'investment_name' => 'required|string|max:255',
            'agreement_number' => 'required|string|max:255|unique:investors,agreement_number',
            'agreement_date' => 'required|date',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $investor = Investor::create($request->except(['created_at', 'updated_at', 'id']));

        activity()
            ->performedOn($investor)
            ->withProperties(
                collect($investor)
                    ->except(['id', 'created_at', 'updated_at']),
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
        $investor = Investor::find($id);

        if (!$investor) {
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
            'investment_name' => 'required|string|max:255',
            'agreement_number' => 'required|string|max:255|unique:investors,agreement_number,'.$id,
            'agreement_date' => 'required|date',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $original = $investor->getOriginal();

        $investor->update($request->except(['created_at', 'updated_at', 'id']));

        $changes = collect($investor->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($investor)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['updated_at'])
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['updated_at'])
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
        $investor = Investor::find($id);

        if (!$investor) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data Investor tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        activity()
            ->performedOn($investor)
            ->withProperties(
                collect($investor)
                    ->except(['id', 'created_at', 'updated_at'])
            )
            ->event('deleted')
            ->log('deleted');

        $investor->delete();

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }
}
