<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admins.book.index');
    }

       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetBooks()
    {
        $eloquent = Book::select('id','filename','status');
        return DataTables::eloquent($eloquent)
            ->editColumn('status', function ($book) {
                return '<span class="badge badge-primary">'.$book->status.'</span>';
            })
            ->addColumn('Action', function ($book) {
                if (auth()->user()->is_admin) {
                    $btn = '<button data-url="' . route('books.destroy', $book->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['status', 'Action'])
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.book.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $request)
    {
        try{
            $data = [
                'filename' => $request->filename,
                'path' => config('lfm.public_dir').$request->filename
            ];
            $book = Book::create($data);
            if($book instanceof Book){
                Storage::disk(config('lfm.disk'))->makeDirectory($book->path);
                return redirect()->route('books.index')->withFlashSuccess('The Book Added Success');
            }
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if ($book instanceof Book) {
            $directory = $book->path;
            if($book->delete()){
                Storage::deleteDirectory($directory);
                return redirect()->route('books.index')->withFlashSuccess('The Book Deleted Success');
            }
            return redirect()->back()->withFlashDanger('There were errors. Please try again.');
        }
        return redirect()->back()->withFlashDanger('The ID \'s Book is not found');
    }
}
