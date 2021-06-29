<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Volume;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;

class PageController extends Controller
{
    public function ajaxGetPages(Request $request){
        $volume = $request->volume;
        $eloquent = Page::with(['rawUser','cleanUser','typeUser','sfxUser','checkUser'])->where('volume_id',$request->volume)->selectRaw('*,CAST( filename AS unsigned) AS id_filename ');
        return DataTables::eloquent($eloquent)
            ->filterColumn('id_filename', function($query, $keyword) {
            })
            ->editColumn('id', function ($page) use ($request) {
                $column = $request->type_down ?? '';
                switch($column){
                    case 'Raw':
                        $status = $page->raw;
                    break;
                    case 'Clean':
                        $status = $page->clean;
                    break;
                    case 'Type':
                        $status = $page->type;
                    break;
                    case 'SFX':
                        $status = $page->sfx;
                    break;
                    case 'Check':
                        $status = $page->check;
                    break;
                    default:
                        $status = '';
                }

                return  $status == 'done' ? '<input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom download-page-id">' : 'N/E';
            })
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
                    // $btn = '<a href="#" data-url="' . route('pages.undoTask', $page->id) . '" class="btn btn-sm undo-task btn-secondary btn-xs mr-2" title="Nhã Việc"><i class="fas fa-undo"></i></a>';
                    $btn = '<a href="#" data-url="' . route('pages.destroy', $page->id) . '" class="btn btn-sm delete btn-danger btn-xs"><i class="fas fa-trash"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['Action', 'raw' , 'clean' , 'type' , 'sfx' , 'check' ,'id'])
            ->toJson();
    }

    public function createOld(Request $request){
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_old',compact('volume'));

    }

    public function createRaw(Request $request){
        if(strpos(auth()->user()->role_multi,'Clean') === false && strpos(auth()->user()->role_multi,'Raw') === false){
            return redirect()->back()->withFlashDanger('Not permission!');
        } 
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_raw',compact('volume'));

    }

    public function createClean(Request $request){
        if(strpos(auth()->user()->role_multi,'Clean') === false && strpos(auth()->user()->role_multi,'Type') === false){
            return redirect()->back()->withFlashDanger('Not permission!');
        } 
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_clean',compact('volume'));

    }

    public function createType(Request $request){
        if(strpos(auth()->user()->role_multi,'SFX') === false && strpos(auth()->user()->role_multi,'Type') === false){
            return redirect()->back()->withFlashDanger('Not permission!');
        } 
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_type',compact('volume'));

    }

    public function createSFX(Request $request){
        if(strpos(auth()->user()->role_multi,'SFX') === false && strpos(auth()->user()->role_multi,'Check') === false){
            return redirect()->back()->withFlashDanger('Not permission!');
        } 
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_sfx',compact('volume'));

    }

    public function createCheck(Request $request){
        if(!auth()->user()->is_admin){
            return redirect()->back()->withFlashDanger('Not permission!');
        } 
        $volume = Volume::find($request->volume);
        
        return view('admins.page.create_check',compact('volume'));

    }
    public function addTask(Request $request,$idVolume){
        try{
        DB::beginTransaction();
        // ini_set('max_execution_time', 300); 
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
                if(strpos(auth()->user()->role_multi,'Clean') === false){
                    return redirect()->back()->withFlashDanger('Not permission!');
                } 
                // $subFolder = config('lfm.vol.raw');
                $pages->update(
                    [
                        $arrayKeyVol[1] => 'doing',
                        $arrayKeyVol[1].'_id' => auth()->id()
                    ]
                    );
                break;
            case 'type':
                if(strpos(auth()->user()->role_multi,'Type') === false){
                    return redirect()->back()->withFlashDanger('Not permission!');
                } 
                // $subFolder = config('lfm.vol.clean');
                $pages->update(
                    [
                        $arrayKeyVol[2] => 'doing',
                        $arrayKeyVol[2].'_id' => auth()->id()
                    ]
                    );
                break;
            case 'sfx':
                if(strpos(auth()->user()->role_multi,'SFX') === false){
                    return redirect()->back()->withFlashDanger('Not permission!');
                } 
                // $subFolder = config('lfm.vol.type');
                $pages->update(
                    [
                        $arrayKeyVol[3] => 'done',
                        $arrayKeyVol[3].'_id' => auth()->id()
                    ]
                    );
                $pagesSearch = $pages->get()->pluck('filename');
                $folderPath = $volume->path.'/'.config('lfm.vol.type').'/';
                $newFilePath = $volume->path.'/'.config('lfm.vol.sfx').'/';
                $filesDone = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->whereIn('filename',$pagesSearch);
                foreach($filesDone as $file){
                        $publicFilePath = '/'.$file['path'];
                        Storage::disk(config('lfm.disk'))->move($publicFilePath,$newFilePath.$file['basename']);
                }
                break;
            // case 'check':
            //     // $subFolder = config('lfm.vol.sfx');
            //     $pages->update(
            //         [
            //             $arrayKeyVol[4] => 'doing',
            //             $arrayKeyVol[4].'_id' => auth()->id()
            //         ]
            //         );
            //     break;
            default:
            DB::rollBack();
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
        DB::commit();
        return redirect()->back()->withFlashSuccess('Get the tasks successfully!');
        // $pagesSearch = $pages->get()->pluck('filename');
        // $pathFolderDownload = $volume->path.'/'.$subFolder.'/';
        // // $filesDown =$request->type_task === 'clean' ? collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('filename',$pagesSearch) : 
        // // collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->where('extension','psd')->whereIn('filename',$pagesSearch);
        // $filesDown = collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('filename',$pagesSearch);
        // if(count($filesDown) > 1){
        //     // $totalFile = count($filesDown);
        //     // session()->put('process_zip', 0);
        //     $zip = new ZipArchive();
        //     $zipFileName = 'download.zip';
        //     if ($zip->open(config('filesystems.disks.private.root').'/'.$zipFileName, ZipArchive::CREATE) === TRUE)
        //     {   
        //         foreach ($filesDown as $key => $value) {
        //             $relativeNameInZipFile = $value['basename'];
        //             $zip->addFile(config('filesystems.disks.private.root').'/'.$value['path'], $relativeNameInZipFile);
        //             // session()->put('process_zip', round((($key+1)/$totalFile),2));
        //         }
        //         $zip->close();
        //     }
        //     // session()->forget('process_zip');
        //     DB::commit();
        //     return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$zipFileName);
        // }else{
        //     if(count($filesDown) == 0){
        //         DB::rollBack();
        //         return redirect()->back()->withFlashDanger('Can\'t find the file in the folder');
        //     }
        //     DB::commit();
        //     return redirect()->back()->withPathDownload(config('filesystems.disks.private.root').'/'.$filesDown->first()['path']);
        // }
    } catch (\Exception $e){
        DB::rollBack();
        return redirect()->back()->withFlashDanger('There were errors. Please try again.');
    }
    }

    public function downTask(Request $request,$idVolume){
        try{
            ini_set('max_execution_time', 300);
            if(!auth()->user()->is_admin){
                switch($request->type_task){
                    case 'Raw':
                        if(strpos(auth()->user()->role_multi,'Clean') === false){
                            return redirect()->back()->withFlashDanger('Not permission!');
                        } 
                    break;
                    case 'Clean':
                        if(strpos(auth()->user()->role_multi,'Type') === false){
                            return redirect()->back()->withFlashDanger('Not permission!');
                        }  
                    break;
                    case 'Type':
                        if(strpos(auth()->user()->role_multi,'SFX') === false){
                            return redirect()->back()->withFlashDanger('Not permission!');
                        }  
                    break;
                    case 'SFX':
                        if(strpos(auth()->user()->role_multi,'Check') === false){
                            return redirect()->back()->withFlashDanger('Not permission!');
                        }  
                    break;
                    default:
                    return redirect()->back()->withFlashDanger('Not permission!');
                } 
            }
            $volume = Volume::find($idVolume);
            $arrayPages = explode(',',$request->id_tasks);
            
            $pages = Page::whereIn('id',$arrayPages);
            $pagesSearch = $pages->get()->pluck('filename');
            $subFolder = config('lfm.vol')[strtolower($request->type_task)];
            $pathFolderDownload = $volume->path.'/'.$subFolder.'/';

            $filesDown = collect(Storage::disk(config('lfm.disk'))->listContents($pathFolderDownload,false))->whereIn('filename',$pagesSearch);
            if(count($filesDown) > 1){
                $zip = new ZipArchive();
                $zipFileName = 'archive_'.auth()->id().'_'.time().'.zip';
                if ($zip->open(config('filesystems.disks.private.root').'/'.$zipFileName, ZipArchive::CREATE) === TRUE)
                {   
                    foreach ($filesDown as $key => $value) {
                        $relativeNameInZipFile = $value['basename'];
                        $zip->addFile(config('filesystems.disks.private.root').'/'.$value['path'], $relativeNameInZipFile);
                        $zip->setCompressionName($relativeNameInZipFile, ZipArchive::CM_STORE);
                        if($key % 20 == 0){
                            $zip->close();
                            $zip->open(config('filesystems.disks.private.root').'/'.$zipFileName);
                        }
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
            }catch(\Exception $e){
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
        try{
            $page = Page::with('volume')->find($id);

            if ($page instanceof Page) {
                $directory = $page->volume->path;
                foreach(config('lfm.volume') as $file ){
                    $filesDelete = collect(Storage::disk(config('lfm.disk'))->listContents($directory.'/'.$file,false))->whereIn('filename',$page->filename)->first();
                    if(isset($filesDelete)){
                        Storage::disk(config('lfm.disk'))->delete($filesDelete['path']);
                    }
                }
                if($page->delete()){
                    return redirect()->back()->withFlashSuccess('The Page Deleted Success');
                }
                return redirect()->back()->withFlashDanger('There were errors. Please try again.');
            }
            return redirect()->back()->withFlashDanger('The Page Not Found');
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function rejectCheck(Request $request){
        try{
            if(strpos(auth()->user()->role_multi,'Check') === false){
                return response()->json(['code'=> 404]);
            }  
            $fileName = $request->fileName;
            $volume_id = $request->volume_id;
            $note = $request->note;
            $page = Page::with('volume')->where('volume_id',$volume_id)->where('filename',$fileName)->first();
            if($page instanceof Page){
                $data = [
                    'check' => 'doing',
                    'check_id' => auth()->id(),
                ];
                if($note){
                    $data['note'] = $note;
                }
                $page->update($data);
                return response()->json(['code'=> 200]);
            }
        }catch(\Exception $e){
            return response()->json(['code'=> 500]);
        }
    }

    public function doneCheck(Request $request){
        DB::beginTransaction();
        try{
            if(strpos(auth()->user()->role_multi,'Check') === false){
                return response()->json(['code'=> 404]);
            }  
            $fileName = $request->fileName;
            $volume_id = $request->volume_id;

            $page = Page::with('volume')->where('volume_id',$volume_id)->where('filename',$fileName)->first();
        if($page instanceof Page){
            $page->update([
                'check' => 'done',
                'check_id' => auth()->id()
            ]);
            $folderPath = $page->volume->path.'/'.config('lfm.vol.sfx');
            $newFilePath = $page->volume->path.'/'.config('lfm.vol.check').'/'.$page->filename;
            $filesDone = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->where('filename',$page->filename)->first();
            $publicFilePath = '/'.$filesDone['path'];
            if($filesDone['extension'] === 'psd'){
                \Image::configure(array('driver' => 'imagick'));
                $file = \Image::make(config('filesystems.disks.private.root').$publicFilePath)->encode('png');
                $file->save(config('filesystems.disks.private.root').'/'.$newFilePath.'.png');
            }else{
                Storage::disk(config('lfm.disk'))->move($publicFilePath,$newFilePath.'.'.$filesDone['extension']);
            }
            DB::commit();
            return response()->json(['code'=>200]);
        }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['code'=> 500]);
        }
    }

    public function checkProcessZip(){
        if(session()->has('process_zip')){

            return response()->json(['process'=>session('process_zip')]);
        }else{
            return response()->json(['process'=> 1]);
        }
    }

    public function undoTask(Request $request,$idVolume){
        try{
            DB::beginTransaction();
            // ini_set('max_execution_time', 300); 
            $volume = Volume::find($idVolume);
            if($volume->status === 'completed'){
                return redirect()->back()->withFlashWarning('The volume was completed!');
            }
            $arrayPages = explode(',',$request->id_tasks);
            
            $pages = Page::whereIn('id',$arrayPages);
            if($pages->get()->where($request->type_task,'!=','doing')->count() > 0){
                return redirect()->back()->withFlashDanger('There were errors. Please try again.');
            }
            $arrayKeyVol = array_keys(config('lfm.volume'));
            switch($request->type_task){
                case 'clean':
                    $pages->update(
                        [
                            $arrayKeyVol[1] => 'doing',
                            $arrayKeyVol[1].'_id' => auth()->id()
                        ]
                        );
                    break;
                case 'type':
                    $pages->update(
                        [
                            $arrayKeyVol[2] => 'doing',
                            $arrayKeyVol[2].'_id' => auth()->id()
                        ]
                        );
                    break;
                case 'sfx':
                    $pages->update(
                        [
                            $arrayKeyVol[3] => 'doing',
                            $arrayKeyVol[3].'_id' => auth()->id()
                        ]
                        );
                    break;
                // case 'check':
                //     $pages->update(
                //         [
                //             $arrayKeyVol[4] => 'doing',
                //             $arrayKeyVol[4].'_id' => auth()->id()
                //         ]
                //         );
                //     break;
                default:
                DB::rollBack();
                return redirect()->back()->withFlashDanger('There were errors. Please try again.');
            }
            DB::commit();
            return redirect()->back()->withFlashSuccess('Get the tasks successfully!');
        } catch (\Exception $e){
            DB::rollBack();
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function noteType(Request $request){
        try{
            $fileName = $request->fileName;
            $volume_id = $request->volume_id;
            $note_type = $request->note_type;
            $page = Page::with('volume')->where('volume_id',$volume_id)->where('filename',$fileName)->first();
            if($page instanceof Page){
                $data['note_type'] = '';
                if($note_type){
                    $data['note_type'] = $note_type;
                }
                $page->update($data);
                return response()->json(['code'=> 200]);
            }
        }catch(\Exception $e){
            return response()->json(['code'=> 500]);
        }
    }

    public function passType(Request $request){
        DB::beginTransaction();
        try{
            $fileName = $request->fileName;
            $volume_id = $request->volume_id;

            $page = Page::with('volume')->where('volume_id',$volume_id)->where('filename',$fileName)->first();
        if($page instanceof Page){
            $page->update([
                'sfx' => 'done',
                'sfx_id' => auth()->id()
            ]);
            $folderPath = $page->volume->path.'/'.config('lfm.vol.type');
            $newFilePath = $page->volume->path.'/'.config('lfm.vol.sfx').'/'.$page->filename;
            $filesDone = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->whereIn('filename',$page->filename)->first();
            $publicFilePath = config('filesystems.disks.private.root').'/'.$filesDone['path'];
            Storage::disk(config('lfm.disk'))->move($publicFilePath,$newFilePath.'.'.$filesDone['extension']);
            DB::commit();
            return response()->json(['code'=>200]);
        }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['code'=> 500]);
        }
    }
}
