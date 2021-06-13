<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeRequest;
use App\Models\Book;
use App\Models\Volume;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $volumes = [];
        if(old('volume')){
        $volumes = Volume::where('book_id',old('volume'))->get();
        }

        $books = Book::all();
        return view('home',compact('books','volumes'));
    }

    public function goToVolDetail(HomeRequest $request){
        return redirect()->route('volumes.detail',['id'=>$request->volume]);
    }
}
