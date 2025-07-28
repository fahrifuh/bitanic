<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeddbackController extends Controller
{
    public function store(Request $request) : JsonResponse {
        $request->validate([
            'platform' => 'required|string|max:255',
            'reviews' => 'required|string|max:1000'
        ]);

        Feedback::create(
            $request->only(['platform', 'reviews']) +
            [
                'user_id' => auth()->id()
            ]
        );

        return response()->json([
            'message' => 'Feedback berhasil disimpan'
        ], 200);
    }
}
