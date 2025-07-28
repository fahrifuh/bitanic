<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::query()
            ->with(['user:id,name'])
            ->select(['id', 'user_id', 'platform', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.feedback.regular.index', compact('feedback'));
    }

    public function show(Feedback $feedback) : View {
        $feedback->load('user:id,name');
        return view('bitanic.feedback.regular.show', compact('feedback'));
    }

    public function destroy(Feedback $feedback) : JsonResponse
    {
        $feedback->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil dihapus"
        ], 200);
    }
}
