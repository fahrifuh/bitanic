<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request, Member $member) : View {
        $type = $request->query('type', null);

        $subscriptions = Subscription::query()
            ->with('user.farmer')
            ->where('member_id', $member->id)
            ->when($type, function($query, $type){
                switch ($type) {
                    case 'current':
                        return $query->whereDate('expired', '>', now()->format('Y-m-d'))
                            ->where('is_canceled', '0')
                            ->where('status', 'settlement');
                        break;
                    case 'canceled':
                        return $query->where('is_canceled', '1')
                            ->where('status', 'settlement');
                        break;
                    case 'history':
                        return $query->where('status', 'settlement');
                        break;
                }
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.member.subscription-users', compact('subscriptions', 'member', 'type'));
    }
}
