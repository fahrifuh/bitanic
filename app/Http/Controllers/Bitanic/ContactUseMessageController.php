<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Mail\ReplyMessage;
use App\Models\ContactUsMessage;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactUseMessageController extends Controller
{
    public function index()
    {
        $messages = ContactUsMessage::query();

        if (request()->query('search')) {
            $search = request()->query('search');
            $messages = $messages->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', $search);
            });
        }

        if (request()->query('status') && in_array(request()->query('status'), ['sudah', 'belum'])) {
            $status = request()->query('status') == 'sudah' ? 1 : 0;
            $messages = $messages->where(function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        $data['data'] = $messages->paginate(10)->withQueryString();

        return view('bitanic.contact-us-message.index', $data);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'message' => 'required|string|max:500',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'message.required' => 'Pesan harus diisi',
            'g-recaptcha-response.required' => 'Silakan centang captcha',
            'g-recaptcha-response.captcha' => 'Captcha tidak valid atau kedaluwarsa',
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        ContactUsMessage::create($request->only(['name', 'email', 'message']) + [
            'created_at' => now('Asia/Jakarta'),
            'updated_at' => null
        ]);

        return response()->json([
            'message' => 'Berhasil'
        ]);
    }

    public function destroy($id)
    {
        $data = ContactUsMessage::find($id);

        if (!$data) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data pesan tidak ditemukan'],
                        'data' => $data,
                        'id' => $id,
                    ],
                ],
                404,
            );
        }

        activity()
            ->performedOn($data)
            ->withProperties(['name', $data->name])
            ->event('deleted')
            ->log('deleted');

        $data->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    public function destroyAll()
    {
        $data = ContactUsMessage::get();

        if (!$data) {
            return back()->with('failed', 'Data kosong');
        }

        activity()
            ->performedOn($data[0])
            ->withProperties(['name', 'Delete Semua Data'])
            ->event('deleted')
            ->log('deleted');

        DB::table('contact_us_messages')->truncate();

        return back()->with('success', 'Berhasil');
    }

    public function changeStatus(Request $request, $id)
    {
        $data = ContactUsMessage::find($id);

        if (!$data) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $data->status = $data->status == 0 ? 1 : 0;
        $data->save();

        return response()->json([
            'message' => 'Berhasil',
            'status' => $data->status
        ], 200);
    }

    public function createMessage(ContactUsMessage $contactUsMessage)
    {
        return view('bitanic.contact-us-message.store-answear', compact('contactUsMessage'));
    }

    public function storeMessage(Request $request, ContactUsMessage $contactUsMessage)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:255']
        ]);

        try {
            // Mail::to($contactUsMessage->email)->send(new ReplyMessage($request->message));
            $emailView = new ReplyMessage($request->message);
            $emailViewRendered = $emailView->render();
            BrevoMailer::send($contactUsMessage->email, $contactUsMessage->name, 'Balasan dari Pesan Anda', $emailViewRendered);

            $contactUsMessage->status = $contactUsMessage->status == 0 ? 1 : 0;
            $contactUsMessage->save();

            return redirect()->route('bitanic.contact-us-message.index')->with('success', "Berhasil dikirim");
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
            return back()->withErrors($th->getMessage())->withInput();
        }
    }
}
