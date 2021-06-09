<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
class FileManagerController extends Controller
{
    public function index(){
        return view('admins.file_manager.index');
    }

    public function ajaxSaveFile(Request $request){
        $id = Volume::latest()->first() ?? 1;
        if ($request->hasfile('files')) {
            $arrayImages = [];
            $images = $request->file('files');
            foreach($images as $image) {
                // dd($image);
                $name = $image->getClientOriginalName();
                // $filePath = $image->getPathName();
                $path = $image->storeAs($id.'/'.$request->type_folder ?? 'raw', $name, 'public');
                // Storage::cloud()->put('1HBexeDSMO_F5NJouFkIc80NHnRTGBTY1/1f1dLIW2lwk-QKn0j4r2YflCEI8FSiGJO/1HpxuQo5l8wCVp3ZGO6FcX3gD3oJQJ-i3/'.$name, fopen($filePath, 'r+'));
                $arrayImages [] = '/storage/'.$path;
            }
            
            return response()->json(['images'=> $arrayImages],200);
        }
    }
}
