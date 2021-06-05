<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Volume;
use Illuminate\Http\Request;
use PDO;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function ajaxGetPages(){
        $eloquent = Page::with(['rawUser','cleanUser','typeUser','sfxUser','checkUser']);
        return DataTables::eloquent($eloquent)
            ->editColumn('raw', function ($page) {
                return showPageStatus($page,'raw');
            })
            ->editColumn('clean', function ($page) {
                return showPageStatus($page,'clean');
            })
            ->editColumn('type', function ($page) {
                return showPageStatus($page,'type');
            })
            ->editColumn('sfx', function ($page) {
                return showPageStatus($page,'sfx');
            })
            ->editColumn('check', function ($page) {
                return showPageStatus($page,'check');
            })
            ->addColumn('Action', function ($page) {
                $btn = '<a href="' . route('volumes.detail', $page->id) . '" class="btn btn-sm btn-primary mr-2"><i class="far fa-eye"></i></a>';
                if (auth()->user()->is_admin) {
                    $btn .= '<a href="' . route('volumes.edit', $page->id) . '" class="btn btn-sm btn-warning mr-2"><i class="far fa-edit"></i></a>';
                    $btn .= '<a href="#" data-url="' . route('volumes.destroy', $page->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['Action', 'status'])
            ->toJson();
    }

    public function createRaw(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_raw',compact('volume'));

    }

    public function storeRaw(Request  $request){

    }
}
