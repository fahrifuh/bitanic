<?php

namespace App\Http\Controllers\Halter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LogCageController extends Controller
{
    function index() : View {
        $logs = DB::table('cage_logs')
            ->orderByDesc('created_at')
            ->paginate(10);
     
        return view('halter.cage.log', [
            'logs' => $logs
        ]);
    }
}
