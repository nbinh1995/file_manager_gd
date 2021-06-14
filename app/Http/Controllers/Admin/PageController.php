<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Volume;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;

class PageController extends Controller
{
    public function ajaxGetPages(Request $request){
        $volume = $request->volume;
        $eloquent = Page::with(['rawUser','cleanUser','typeUser','sfxUser','checkUser'])->where('volume_id',$request->volume);
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
            ->addColumn('Action', function ($page) use($volume) {
                $btn = '';
                if (auth()->user()->is_admin) {
                    $btn = '<a href="#" data-url="' . route('pages.destroy', $page->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['Action', 'raw' , 'clean' , 'type' , 'sfx' , 'check'])
            ->toJson();
    }

    public function createOld(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_old',compact('volume'));

    }

    public function createRaw(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_raw',compact('volume'));

    }

    public function createClean(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_clean',compact('volume'));

    }

    public function createType(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_type',compact('volume'));

    }

    public function createSFX(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_sfx',compact('volume'));

    }

    public function createCheck(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_check',compact('volume'));

    }
    public function addTask(Request $request,$idVolume){
        DB::beginTransaction();
        try{
        $volume = Volume::find($idVolume);
        if($volume->status === 'completed'){
            return redirect()->back()->withFlashWarning('The volume was completed!');
        }
        $arrayPages = explode(',',$request->id_tasks);
        
        $pages = Page::whereIn('id',$arrayPages);
        if($pages->get()->where($request->type_task,'!=','pending')->count() > 0){
            return redirect()->back()->withFlashWarning('The task has been accepted by someone!');
        }
        $arrayKeyVol = array_keys(config('lfm.volume'));
        switch($request->type_task){
            case 'clean':
                $subFolder = config('lfm.vol.raw');
                $pages->update(
                    [
                        $arrayKeyVol[1] => 'doing',
                        $arrayKeyVol[1].'_id' => auth()->id()
                    ]
                    );
                break;
            case 'type':
                $subFolder = config('lfm.vol.clean');
                $pages->update(
                    [
                        $arrayKeyVol[2] => 'doing',
                        $arrayKeyVol[2].'_id' => auth()->id()
                    ]
                    );
                break;
            case 'sfx':
                $subFolder = config('lfm.vol.type');
                $pages->update(
                    [
                        $arrayKeyVol[3] => 'doing',
                        $arrayKeyVol[3].'_id' => auth()->id()
                    ]
                    );
                break;
            case 'check':
                $subFolder = config('lfm.vol.sfx');
                $pages->update(
                    [
                        $arrayKeyVol[4] => 'doing',
                        $arrayKeyVol[4].'_id' => auth()->id()
                    ]
                    );
                break;
            default:
            DB::rollBack();
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }

        $pagesSearch = $pages->get()->pluck('filename');
        $pathFolderDownload = $volume->path.'/'.$subFolder.'/';
        // $filesDown =$request->type_task === 'clean' ? collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('filename',$pagesSearch) : 
        // collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->where('extension','psd')->whereIn('filename',$pagesSearch);
        $filesDown = collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('filename',$pagesSearch);
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
            DB::commit();
            return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$zipFileName);
        }else{
            if(count($filesDown) == 0){
                DB::rollBack();
                return redirect()->back()->withFlashDanger('Can\'t find the file in the folder');
            }
            DB::commit();
            return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$filesDown->first()['path']);
        }
    } catch (\Exception $e){
        DB::rollBack();
        return redirect()->back()->withFlashDanger('There were errors. Please try again.');
    }
    }

    public function downloadFile(Request $request)
    {   
        $path_download = $request->path_download;
        if(strpos($path_download,'.zip') != false){
            return response()->download($request->path_download)->deleteFileAfterSend(true);
        }else{
            return response()->download($request->path_download);
        }
    }

    public function destroy($id){
        $page = Page::with('volume')->find($id);

        if ($page instanceof Page) {
            $directory = $page->volume->path;
            foreach(config('lfm.vol') as $file ){
                Storage::disk(config('lfm.disk'))->delete($directory.'/'.$file.'/'.$page->filename.'.png');
                Storage::disk(config('lfm.disk'))->delete($directory.'/'.$file.'/'.$page->filename.'.psd');
            }
            if($page->delete()){
                return redirect()->back()->withFlashSuccess('The Volume Deleted Success');
            }
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
        return redirect()->back()->withFlashDanger('The ID \'s Book is not found');
    }
}
