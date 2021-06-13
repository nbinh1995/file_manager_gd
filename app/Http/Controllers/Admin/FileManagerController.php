<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FileManagerController extends Controller
{
    public function index(){
        if(!File::exists(config('filesystems.disks.private.root').'/files')){
            File::makeDirectory(config('filesystems.disks.private.root').'/files',0777);
        }
        if(!File::exists(config('filesystems.disks.private.root').'/files/shares')){
            File::makeDirectory(config('filesystems.disks.private.root').'/files/shares',0777);
        }
        return view('admins.file_manager.index');
    }

    public function ajaxSaveFile(Request $request){
        $id = Volume::latest()->first() ?? 1;
        if ($request->hasfile('files')) {
            $arrayImages = [];
            $images = $request->file('files');
            foreach($images as $image) {
                $name = $image->getClientOriginalName();
                // $filePath = $image->getPathName();
                $path = $image->storeAs($id.'/'.$request->type_folder ?? 'raw', $name, 'public');
                // Storage::cloud()->put('1HBexeDSMO_F5NJouFkIc80NHnRTGBTY1/1f1dLIW2lwk-QKn0j4r2YflCEI8FSiGJO/1HpxuQo5l8wCVp3ZGO6FcX3gD3oJQJ-i3/'.$name, fopen($filePath, 'r+'));
                $arrayImages [] = '/storage/'.$path;
            }
            
            return response()->json(['images'=> $arrayImages],200);
        }
    }

    public function showImage(Request $request){
        $page_id = $request->page_id;
        $typeFolder = $request->type;
        $page = Page::with('volume')->find($page_id);
        $folderPath = $page->volume->path.'/'.$typeFolder;
        $filesShow = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->whereIn('filename',$page->filename)->first();
        $publicFilePath = config('filesystems.disks.private.root').'/'.$filesShow['path'];
       
        if($filesShow['extension'] === 'psd'){
        \Image::configure(array('driver' => 'imagick'));
        $file = \Image::make($publicFilePath)->encode('png');
        $type = 'image/png';
        }else{
            $file = File::get($publicFilePath);
            $type = File::mimeType($publicFilePath);
        }
        $response = response()->make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function showUrlManager(Request $request){
        $publicFilePath = config('filesystems.disks.private.root').'/'.$request->path;
        if(strpos($publicFilePath,'psd') !== false){
        \Image::configure(array('driver' => 'imagick'));
        $file = \Image::make($publicFilePath)->encode('png');
        $type = 'image/png';
        }else{
            $file = File::get($publicFilePath);
            $type = File::mimeType($publicFilePath);
        }
        $response = response()->make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function showPrevImage(Request $request){
        $page_id = $request->page_id;
        $volume_id = $request->volume_id;
        $type = $request->type;
        $column = strtolower($type); 
        $prev_page = Page::with('volume')->where($column,'done')->where('volume_id',$volume_id)->where('id', '<', $page_id)->max('id');
        $url ='';
        $code = 404;
        if(!is_null($prev_page)){
            $code=200;
            $url = route('file-manager.showImage',['type'=>$type,'page_id'=>$prev_page]);
        }

        return response()->json(['src' => $url,'code' => $code]);
    }

    public function showNextImage(Request $request){
        $page_id = $request->page_id;
        $volume_id = $request->volume_id;
        $type = $request->type;
        $column = strtolower($type); 
        $next_page = Page::with('volume')->where($column,'done')->where('volume_id',$volume_id)->where('id', '>', $page_id)->min('id');
        $url ='';
        $code = 404;
        if(!is_null($next_page)){
            $code=200;
            $url = route('file-manager.showImage',['type'=>$type,'page_id'=>$next_page]);
        }

        return response()->json(['src' => $url,'code' => $code]);
    }

    public function downloadFile(Request $request){
        $pathFolderDownload = $request->dir.'/';
        $arrayFileName = explode(',',$request->filenames);
        $filesDown = collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('basename',$arrayFileName);
    
        if(count($filesDown) > 1){
            $zip = new ZipArchive();
            $zipFileName = config('lfm.public_dir').'download.zip'; 
            if ($zip->open(config('filesystems.disks.private.root').'/'.$zipFileName, ZipArchive::CREATE) === TRUE)
            {   
                foreach ($filesDown as $key => $value) {
                    $relativeNameInZipFile = $value['basename'];
                    $zip->addFile(config('filesystems.disks.private.root').'/'.$value['path'], $relativeNameInZipFile);
                }
                $zip->close();
            }
            return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$zipFileName);
        }else{
            return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$filesDown->first()['path']);
        }
    }
}
