<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Pest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pest = Pest::query()
            ->with(['crop']);

        if (request()->query('search')) {
            $search = request()->query('search');
            $pest = $pest->where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('pest_type', 'LIKE', '%'.$search.'%');
        }

        $data['data'] = $pest->orderBy('pest_type')->paginate(10)->withQueryString();

        return view('bitanic.pest.index', $data);
    }

    public function create() {
        $crops = Crop::query()
            ->pluck('crop_name', 'id');

        return view('bitanic.pest.create', compact('crops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'pest_type'    => 'required|string|max:255|unique:pests,pest_type',
            'crop_id'       => 'required|integer|exists:crops,id',
            'features'    => 'required|string|max:1000',
            'symptomatic'    => 'required|string|max:1000',
            'precautions'    => 'required|string|max:1000',
            'countermeasures'    => 'required|string|max:1000',
            'image'          => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $foto = image_intervention($request->file('image'), 'bitanic-photo/pests/');

        Pest::create($request->only([
            'crop_id',
            'pest_type',
            'features',
            'symptomatic',
            'precautions',
            'countermeasures',
        ])+[
            'name' => '',
            'picture' => $foto
        ]);

        return redirect()->route('bitanic.pest.index')->with('success', 'berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pest $pest)
    {
        $pest->load('crop');

        return view('bitanic.pest.show', compact('pest'));
    }

    public function edit(Pest $pest) {
        $crops = Crop::query()
            ->pluck('crop_name', 'id');

        return view('bitanic.pest.edit', compact('crops', 'pest'));
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
        $data = Pest::findOrFail($id);

        $v = Validator::make($request->all(),[
            'pest_type'    => 'required|string|max:255|unique:pests,pest_type,' . $id,
            'crop_id'       => 'required|integer|exists:crops,id',
            'features'    => 'required|string|max:1000',
            'symptomatic'    => 'required|string|max:1000',
            'precautions'    => 'required|string|max:1000',
            'countermeasures'    => 'required|string|max:1000',
            'image'          => 'nullable|image|mimes:jpg,png|max:2048',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v->errors());
        }

        if ($request->file('image')) {
            $picture = image_intervention($request->file('image'), 'bitanic-photo/pests/');

            if(File::exists(public_path($data->picture))){
                File::delete(public_path($data->picture));
            }

            $data->picture = $picture;
            $data->save();
        }

        $data->update($request->only([
            'crop_id',
            'pest_type',
            'features',
            'symptomatic',
            'precautions',
            'countermeasures',
        ]));

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $data = Pest::find($id);

      if (!$data) {
        return response()->json([
          'messages' => (object) [
            'text' => ["Data hama tidak ditemukan"]
          ]
        ], 404);
      }

      if(File::exists(public_path($data->picture))){
        File::delete(public_path($data->picture));
      }

      $data->delete();

      session()->flash('success', 'Berhasil dihapus');

      return response()->json([
        'message' => "Berhasil"
      ], 200);
    }
}
