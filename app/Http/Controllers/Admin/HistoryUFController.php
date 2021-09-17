<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HistoryUF;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class HistoryUFController extends Controller
{
    public function index(Request $request){
        $users = User::withTrashed()->get(['id','username']);
        $now = Carbon::now();
        if ($request->filled('firstTime') && preg_match('/([0-9]{4}-([0-9]{2})-([0-9]{2}))/', $request->firstTime)) {
            $firstTime = Carbon::createFromFormat('Y-m-d', $request->firstTime)->format('Y-m-d');
        } else {
            $firstTime = Carbon::createFromDate($now->year, $now->month, 1)->format('Y-m-d');
        }
        if ($request->filled('lastTime') && preg_match('/([0-9]{4}-([0-9]{2})-([0-9]{2}))/', $request->lastTime)) {
            $lastTime = Carbon::createFromFormat('Y-m-d', $request->lastTime)->format('Y-m-d');
        } else {
            $lastTime = $now->format('Y-m-d');
        }
        $user_id = $request->has('user_id') ? $request->user_id : '';

        return view('admins.history.index',compact('users','user_id','firstTime','lastTime'));
    }

    public function ajaxHistories(Request $request){
        $eloquent = HistoryUF::with('user:id,username')->when($request->user_id,function($query) use($request){
            $query->where('user_id',$request->user_id);
        })->when($request->firstTime,function($query) use($request){
            $query->whereDate('created_at','>=',$request->firstTime);
        })->when($request->lastTime,function($query) use($request){
            $query->whereDate('created_at','<=',$request->lastTime);
        })->select('id','user_id','book','volume','page','type','created_at');
        return DataTables::eloquent($eloquent)
        ->editColumn('user_id',function($history){
            return $history->user->username ?? 'Not Found';
        })->toJson();
    }
}
