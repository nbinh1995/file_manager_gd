<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Volume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            ->editColumn('status', function ($volume) {
                return '<span class="badge badge-primary">'.$volume->status.'</span>';
            })
            ->addColumn('Action', function ($volume) {
                $btn = '<a href="' . route('volumes.detail', $volume->id) . '" class="btn btn-sm btn-primary mr-2"><i class="far fa-eye"></i></a>';
                if (auth()->user()->is_admin) {
                    $btn .= '<a href="' . route('volumes.edit', $volume->id) . '" class="btn btn-sm btn-warning mr-2"><i class="far fa-edit"></i></a>';
                    $btn .= '<a href="#" data-url="' . route('volumes.destroy', $volume->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['Action', 'active'])
            ->toJson();
    }

    public function create()
    {
        
        $books = Book::all();

        return view('admins.volume.create',compact('books'));
    }

    public function store(Request $request){
        dd($request->all());
        try{
            $data = [
                'filename' => $request->filename,
                'book_id' => $request->book_id
            ];
            $data['path'] = Book::find($request->book_id)->path;
            $volume = Volume::create($data);
            if($volume instanceof Book){
                foreach(config('lfm.volume') as $filename ){
                    Storage::disk(config('lfm.disk'))->makeDirectory($volume->path.'/'.$filename);
                }
                return redirect()->route('books.index')->withFlashSuccess('The Volume Added Success');
            }
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }
}
