<?php

namespace App\Http\Controllers\Bitanic\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteFeedback;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index() : View
    {
        $feedback = LiteFeedback::query()
            ->with(['lite_user:id,name'])
            ->select(['id', 'lite_user_id', 'platform', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.feedback.lite.index', compact('feedback'));
    }

    public function show(LiteFeedback $liteFeedback) : View {
        $liteFeedback->load('lite_user:id,name');
        return view('bitanic.feedback.lite.show', compact('liteFeedback'));
    }

    public function destroy(LiteFeedback $liteFeedback) : JsonResponse
    {
        $liteFeedback->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil dihapus"
        ], 200);
    }
}
