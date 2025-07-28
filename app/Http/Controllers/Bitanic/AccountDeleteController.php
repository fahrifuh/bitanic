<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountDeleteController extends Controller
{
    public function index()
    {
        $data['data'] = AccountDeletionApplication::paginate(10);

        return view('bitanic.account-detele-application.index', $data);
    }

    public function accept(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required|integer|exists:account_deletion_applications,id',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $data = AccountDeletionApplication::find($request->id);

        $user = User::find($data->user_id);

        $user->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    public function decline(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required|integer|exists:account_deletion_applications,id',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $data = AccountDeletionApplication::find($request->id);

        $data->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
