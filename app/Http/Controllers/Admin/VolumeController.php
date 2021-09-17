<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Volume\VolumeRequest;
use App\Models\Book;
use App\Models\Page;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class VolumeController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::all();
        $book_id = $request->has('book_id') ? $request->book_id : '';
        return view('admins.volume.index',compact('books','book_id'));
    }

    public function ajaxGetVolumes(Request $request){
        $eloquent = Volume::leftJoin('books','volumes.book_id','books.id')->select('volumes.id','volumes.filename','volumes.status','books.filename as bookname','volumes.is_hide')->when($request->book_id,function($query) use($request){
            $query->where('volumes.book_id',$request->book_id);
        });
        return DataTables::eloquent($eloquent)
            ->filterColumn('bookname', function($query, $keyword) {
                    $query->whereRaw('books.filename like ?', ["%{$keyword}%"]);
            })
            ->editColumn('is_hide', function ($volume) {
                return '<input type="checkbox" value="'.$volume->id.'"  class="is_hide"'.($volume->is_hide ? 'checked' : '').'>';
            })
            ->editColumn('filename', function ($volume) {
                return '<a href="'.route('volumes.detail',['id'=>$volume->id]).'">'.$volume->filename.'</>';
            })
            ->editColumn('status', function ($volume) {
                return '<span class="badge badge-primary py-1 px-2">'.$volume->status.'</span>';
            })
            ->addColumn('Action', function ($volume) {
                $btn = '<a href="' . route('volumes.detail', $volume->id) . '" class="btn btn-sm btn-primary mr-2"><i class="far fa-eye"></i></a>';
                if (auth()->user()->is_admin) {
                    $btn .= '<a href="' . route('volumes.edit', $volume->id) . '" class="btn btn-sm btn-warning mr-2"><i class="far fa-edit"></i></a>';
                    $btn .= '<a href="#" data-url="' . route('volumes.destroy', $volume->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['Action', 'status','filename','is_hide'])
            ->toJson();
    }

    public function create()
    {
        
        $books = Book::all();

        return view('admins.volume.create',compact('books'));
    }

    public function store(VolumeRequest $request)
    {
        try{
            $data = [
                'filename' => $request->filename,
                'book_id' => $request->book_id
            ];
            $data['path'] = Book::find($request->book_id)->path.'/'.convert_name($request->filename);
            $volume = Volume::create($data);
            if($volume instanceof Volume){
                // File::makeDirectory(config('filesystems.disks.private.root').'/'.$volume->path,0777);
                foreach(config('lfm.vol') as $filename ){
                    File::makeDirectory(config('filesystems.disks.private.root').'/'.$volume->path.'/'.$filename,0777,true,true);
                }
                return redirect()->route('volumes.detail',['id' => $volume->id])->withFlashSuccess('The Volume Added Success');
            }
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function detail($id){
        $volume = Volume::with('book')->find($id);
        if($volume instanceof Volume &&(auth()->user()->is_admin || !$volume->is_hide)){
            return view('admins.volume.detail',compact('volume'));
        }
        return redirect()->back()->withFlashDanger('Not Found!');
    }

    public function edit($id){
        $volume = Volume::find($id);

        return view('admins.volume.edit',compact('volume'));
    }

    public function update(Request $request , $id){
        DB::beginTransaction();
        try{
            $volume = Volume::find($id);
            $pages = Page::where('volume_id',$id);
            if($volume instanceof Volume){
                if($request->status !== 'pending'){
                    Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.clean'));
                    Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.type'));
                    Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.sfx'));
                    $pages_list = $pages->get(['raw_image','clean_image','type_image','sfx_image']);
                    foreach($pages_list as $page){
                        if($page->raw_image){
                            if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->raw_image)){
                                unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->raw_image);
                            }
                        }
                        if($page->clean_image){
                            if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->clean_image)){
                                unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->clean_image);
                            }
                        }
                        if($page->type_image){
                            if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->type_image)){
                                unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->type_image);
                            }
                        }
                        if($page->sfx_image){
                            if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->sfx_image)){
                                unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->sfx_image);
                            }
                        }
                    }
                    $pages->update([
                        'raw_image'=>null,
                        'clean_image' => null,
                        'type_image' => null,
                        'sfx_image' => null,
                        'note' => null,
                        'note_image' => null
                    ]);
                }else{
                    if(!File::exists(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.clean'))){
                        File::makeDirectory(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.clean'),0777);
                    }
                    if(!File::exists(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.type'))){
                        File::makeDirectory(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.type'),0777);
                    }
                    if(!File::exists(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.sfx'))){
                        File::makeDirectory(config('filesystems.disks.private.root').'/'.$volume->path.'/'.config('lfm.vol.sfx'),0777);
                    }
                }
    
                $volume->update($request->only('status'));
                if($volume instanceof Volume){
                    DB::commit();
                    return redirect()->route('volumes.index')->withFlashSuccess('The Volume Updated Success');
                }
                DB::rollBack();
                return redirect()->back()->withFlashDanger('There were errors. Please try again.');
            }
            DB::rollBack();
            return redirect()->back()->withFlashDanger('The Volume Not Found');
        }catch(\Exception $e){
            DB::rollBack();
            dd($e);
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function destroy($id){
        try{
            $volume = Volume::find($id);

            if ($volume instanceof Volume) {
                $directory = $volume->path;
                if($volume->delete()){
                    Storage::disk(config('lfm.disk'))->deleteDirectory($directory);
                    return redirect()->route('volumes.index')->withFlashSuccess('The Volume Deleted Success');
                }
                return redirect()->back()->withFlashDanger('There were errors. Please try again.');
            }
            return redirect()->back()->withFlashDanger('The Volume Not Found');
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function ajaxGetVolumesByBookID(Request $request){
        $book_id = $request->book;
        $volumes = Volume::withCount(['pages','pages_sfx_done','pages_check_done'])->where('book_id',$book_id)->where('is_hide',false)->get();
        
        return response()->json(['volumes'=> $volumes]);
    }

    public function ajaxChangeHideVolume(Request $request){
        try{
            $book_id = $request->id;
            $volume = Volume::find($book_id);
            if($volume instanceof Volume){
                $data=[
                    'is_hide' => $request->hide == 1 ? true : false
                ];
                $volume->update($data);
                return response()->json(['code'=>200]);
            }
            return response()->json(['code'=>404]);
        }catch(\Exception $e){
            return response()->json(['code'=>500]);
        }
    }
}
