<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Volume\VolumeRequest;
use App\Models\Book;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class VolumeController extends Controller
{
    public function index(Request $request)
    {
        return view('admins.volume.index');
    }

    public function ajaxGetVolumes(){
        $eloquent = Volume::leftJoin('books','volumes.book_id','books.id')->select('volumes.id','volumes.filename','volumes.status','books.filename as bookname');
        return DataTables::eloquent($eloquent)
            ->editColumn('filename', function ($volume) {
                return '<a href="'.route('volumes.detail',['id'=>$volume->id]).'">'.$volume->filename.'</a>';
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
            ->rawColumns(['Action', 'status','filename'])
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
            dd($e);
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    public function detail($id){
        $volume = Volume::find($id);
        if($volume instanceof Volume){
            return view('admins.volume.detail',compact('volume'));
        }
        return redirect()->back()->withFlashDanger('Not Found!');
    }

    public function edit($id){
        $volume = Volume::find($id);

        return view('admins.volume.edit',compact('volume'));
    }

    public function update(Request $request , $id){
        $volume = Volume::find($id);

        if($volume instanceof Volume){
            if($request->status !== 'pending'){
                Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.clean'));
                Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.type'));
                Storage::disk(config('lfm.disk'))->deleteDirectory($volume->path.'/'.config('lfm.vol.sfx'));
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
            return redirect()->route('volumes.index');
        }
    }

    public function destroy($id){
        $volume = Volume::find($id);

        if ($volume instanceof Volume) {
            $directory = $volume->path;
            if($volume->delete()){
                Storage::disk(config('lfm.disk'))->deleteDirectory($directory);
                return redirect()->route('volumes.index')->withFlashSuccess('The Volume Deleted Success');
            }
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
        return redirect()->back()->withFlashDanger('The ID \'s Book is not found');
    }

    public function ajaxGetVolumesByBookID(Request $request){
        $book_id = $request->book;
        $volumes = Volume::where('book_id',$book_id)->get();

        return response()->json(['volumes'=> $volumes]);
    }
}
