<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pest;
use Illuminate\Http\Request;

class PestController extends Controller
{
    public function index(Request $request)
    {
        $pests = Pest::query();

        if ($request->query('search')) {
            $search = '%'.$request->query('search').'%';

            $pests = $pests
                ->where('name', 'LIKE', $search)
                ->orWhere('pest_type', 'LIKE', $search);
        }

        $pests = $pests->get(['id', 'name', 'pest_type', 'picture']);

        return response()->json([
            'pests' => $pests,
            'message' => 'Data hama',
            'status' => 200
        ], 200);
    }
}
