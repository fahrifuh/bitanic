<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        $data['events'] = ['login', 'logout','created','updated','deleted'];
        $activity_log = DB::table('activity_log');

        if (request()->query('search')) {
            $search = request()->query('search');
            $users = User::where('name', 'LIKE', '%'.$search.'%')->get(['id']);
            $activity_log = $activity_log->where(function($query)use($users){
                $query->whereIn('causer_id', $users->toArray());
            });
        }

        if (request()->query('event') && in_array(request()->query('event'), $data['events'])) {
            $event = request()->query('event');
            $activity_log = $activity_log->where(function($query)use($event){
                $query->where('event', $event);
            });
        }

        if (request()->query('month') && validateDate(request()->query('month'), 'Y-m')) {
            $date = explode('-', request()->query('month'));
            $year = $date[0];
            $month = $date[1];
            $activity_log = $activity_log->where(function($query)use($year, $month){
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
            });
        }

        $data['data'] = $activity_log->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('bitanic.log-activity.index', $data);
    }
}
