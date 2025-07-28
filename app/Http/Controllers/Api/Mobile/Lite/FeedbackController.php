<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request) : JsonResponse {
        $request->validate([
            'platform' => 'required|string|max:255',
            'reviews' => 'required|string|max:1000'
        ]);

        LiteFeedback::create(
            $request->only(['platform', 'reviews']) +
            [
                'lite_user_id' => auth()->guard('lite')->id()
            ]
        );

        return response()->json([
            'message' => 'Feedback berhasil disimpan'
        ], 200);
    }
}
