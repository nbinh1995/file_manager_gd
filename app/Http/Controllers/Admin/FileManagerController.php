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
        $volume_id = $request->volume_id;
        $fileName = $request->fileName;
        $typeFolder = $request->type;
        $page = Page::with('volume')->where('volume_id',$volume_id)->where('filename',$fileName)->first();
        $folderPath = $page->volume->path.'/'.$typeFolder;
        $filesShow = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->sortByDesc('timestamp')->whereIn('filename',$page->filename)->first();
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
        $publicFilePath = config('filesystems.disks.private.root').$request->filename;
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
        $fileName = (int)$request->fileName;
        $volume_id = $request->volume_id;
        $type = $request->type;
        $column = strtolower($type); 
        $prev_page = Page::where($column,'done')->where('volume_id',$volume_id)->whereRaw('CAST( filename AS unsigned) < ?',$fileName)->selectRaw('filename , CAST( filename AS unsigned) AS id_filename')->get()->max();
        $url ='';
        $code = 404;
        $hasAction = 0;
        if(!is_null($prev_page)){
            if($column === 'sfx'){
                if(Page::where('check','pending')->where('volume_id',$volume_id)->where('filename',$prev_page->filename)->exists()){
                    $hasAction = 1;
                }
            }
            $code=200;
            $url = route('file-manager.showImage',['volume_id'=>$volume_id,'type'=>$type,'fileName'=>$prev_page->filename]);
        }

        return response()->json(['src' => $url,'hasAction'=> $hasAction,'code' => $code]);
    }

    public function showNextImage(Request $request){
        $fileName = (int)$request->fileName;
        $volume_id = $request->volume_id;
        $type = $request->type;
        $column = strtolower($type); 
        $next_page = Page::where($column,'done')->where('volume_id',$volume_id)->whereRaw('CAST( filename AS unsigned) > ?',$fileName)->selectRaw('filename , CAST( filename AS unsigned) AS id_filename')->get()->min();
        $url ='';
        $code = 404;
        $hasAction = 0;
        if(!is_null($next_page)){
            if($column === 'sfx'){
                if(Page::where('check','pending')->where('volume_id',$volume_id)->where('filename',$next_page->filename)->exists()){
                    $hasAction = 1;
                }
            }
            $code=200;
            $url = route('file-manager.showImage',['volume_id'=>$volume_id,'type'=>$type,'fileName'=>$next_page->filename]);
        }

        return response()->json(['src' => $url,'hasAction'=> $hasAction,'code' => $code]);
    }

    public function downloadFile(Request $request){
        ini_set('max_execution_time', 300); 
        $pathFolderDownload = $request->dir;
        $arrayFileName = explode(',',$request->filenames);
        $filesDown = collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('basename',$arrayFileName);
    
        if(count($filesDown) > 1){
            $zip = new ZipArchive();
            $zipFileName = 'download.zip'; 
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
            if(count($filesDown) == 0){
                return redirect()->back()->withFlashDanger('Can\'t find the file in the folder');
            }
            return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$filesDown->first()['path']);
        }
    }
}
