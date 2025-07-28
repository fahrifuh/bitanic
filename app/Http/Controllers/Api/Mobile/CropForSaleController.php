<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CropForSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropForSaleController extends Controller
{
    public function index(Request $request) : JsonResponse
    {
        $name = $request->query('name');
        $cropForSales = CropForSale::query()
            ->when($name, function($query, $name){
                return $query->where('name', 'LIKE', '%' . trim($name) . '%');
            })
            ->orderBy('name')
            ->get(['id', 'picture', 'name', 'days']);

        return response()->json([
            'message' => 'Data tanaman yang bisa dijual',
            'crops' => $cropForSales
        ], 200);
    }
}
