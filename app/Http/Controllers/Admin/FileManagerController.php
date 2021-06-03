<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class FileManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dir = collect(Storage::cloud()->listContents('/', false));
        return view('admins.file_manager.index',compact('dir'));
    }

    public function ajaxGetFolder(Request $request){
        $dir = collect(Storage::cloud()->listContents($request->path, false));
        return response()->json(['dir'=>$dir]);
    }
}
