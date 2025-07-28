<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function currentSubscription() : JsonResponse {
        $subscription = Subscription::query()
            ->with('member')
            ->whereDate('expired', '>', now()->format('Y-m-d'))
            ->where('user_id', auth()->id())
            ->where('is_canceled', '0')
            ->where('status', 'settlement')
            ->first();

        return response()->json([
            'message' => 'User current member subscription',
            'subscription' => $subscription
        ]);
    }

    public function storeSubsciptions(Request $request) {
        $validated = $request->validate([
            'member_id' => 'required|integer|min:0',
            'bank_code' => 'required|string',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $bank = Bank::where('code', $validated['bank_code'])->firstOrFail();
        $bank_fees = 0;

        if ($bank->fees) {
            foreach ($bank->fees as $fee) {
                switch ($fee['type']) {
                    case 0:
                        $bank_fees += $fee['fee'];
                        break;
                    case 1:
                        $bank_fees += ceil($member->fee * ($fee['fee'] / 100));
                        break;
                }
            }
        }

        $subscription = Subscription::query()
            ->whereDate('expired', '>', now()->format('Y-m-d'))
            ->where('user_id', auth()->id())
            ->where('is_canceled', '0')
            ->where('status', 'settlement')
            ->first();

        $code = now()->timestamp . "MEMBER" . Str::random(15);

        $price = $member->fee + $bank_fees;

        $params = $this->buildMidtransParameters([
            'transaction_code' => $code,
            'price' => $price,
            'payment_method' => $validated['bank_code'],
            'item_id' => $validated['member_id'],
            'item_price' => $price,
            'item_name' => $member->name,
        ]);

        $midtrans = $this->callMidtrans($params);

        if ($subscription) {
            $subscription->is_canceled = 1;
            $subscription->save();
        }

        Subscription::create([
            'code' => $code,
            'midtrans_token' => $midtrans['token'],
            'user_id' => auth()->id(),
            'member_id' => $validated['member_id'],
            'expired' => now()->addYear(),
            'status' => 'pending',
            'bank_name' => $bank->name,
            'bank_code' => $bank->code,
            'bank_fee' => $bank_fees,
        ]);

        return response()->json($midtrans);
    }

    public function updateCancelMember(Request $request) {
        $validated = $request->validate([
            'is_accepted' => 'required|accepted',
        ]);

        $subscription = Subscription::query()
            ->whereDate('expired', '>', now()->format('Y-m-d'))
            ->where('user_id', auth()->id())
            ->where('is_canceled', '0')
            ->where('status', 'settlement')
            ->first();

        if (!$subscription) {
            return redirect()->back()->withErrors([
                'subscription' => ['Tidak ada member yang bisa dibatalkan']
            ]);
        }

        $subscription->is_canceled = 1;
        $subscription->save();

        return response()->json([
            'message' => 'Member anda berhasil dibatalkan'
        ]);
    }

    private function callMidtrans(array $params): array
    {
        // \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        // \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED');
        // \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

        $createTransaction = \Midtrans\Snap::createTransaction($params);

        return [
            'redirect_url' => $createTransaction->redirect_url,
            'token' => $createTransaction->token
        ];
    }

    private function buildMidtransParameters(array $params)
    {
        $transactionDetails = [
            'order_id' => $params['transaction_code'],
            'gross_amount' => $params['price']
        ];

        $itemDetails = [
            [
                'id' => $params['item_id'],
                'price' => $params['item_price'],
                'quantity' => 1,
                'name' => $params['item_name']
            ]
        ];

        $user = auth()->user();
        $splitName = $this->splitName($user->name);

        $customerDetails = [
            'first_name' => $splitName['first_name'],
            'last_name' => $splitName['last_name'],
            'phone' => $user->phone_number,
        ];

        $enabledPayment = [
            $params['payment_method']
        ];

        return [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'enabled_payments' => $enabledPayment,
        ];
    }

    private function splitName($fullname)
    {
        $name = explode(' ', $fullname);

        $lastName = count($name) > 1 ? array_pop($name) : $fullname;
        $firstName = implode(' ', $name);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }
}
