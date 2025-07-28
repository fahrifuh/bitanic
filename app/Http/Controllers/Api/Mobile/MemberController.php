<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index() : JsonResponse {
        $members = Member::query()
            ->withCount(['subscriptions as current_active' => function($query){
                $query->whereDate('expired', '>', now()->format('Y-m-d'))
                    ->where('is_canceled', '0')
                    ->where('status', 'settlement')
                    ->where('user_id', auth()->id());
            }])
            ->orderBy('max_commodities')
            ->get();

        return response()->json([
            'message' => 'Members Data',
            'members' => $members,
        ]);
    }
}
