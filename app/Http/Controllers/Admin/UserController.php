<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Mail\CreateUserMail;
use App\Mail\UpdatePassWordUserMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\MessageBag;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function index()
    {
        return view('admins.user.index');
    }

    public function create()
    {
        return view('admins.user.create');
    }

    public function password()
    {
        return view('admins.user.password');
    }

    public function changePassword(PasswordRequest $request)
    {
        if (Hash::check($request->get('old-password'), Auth::user()->password)) {
            $user = User::findOrFail(Auth::user()->id);
            $user->password = Hash::make($request->password);
            $user->save();
            auth()->logout();
            return redirect()->route('login')->withMessageSuccess('Your password successful updated.');
        } else {
            $errors = new MessageBag();
            return redirect()->back()->withErrors($errors->add('old', __('Your old password not correct.')));
        }
    }

    public function store(UserRequest $request)
    {
        try{
            $data = $request->except('password','active','is_admin');
        $data['is_admin'] = $request->filled('is_admin') ? 1 : 0; 
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        if ($user instanceof User) {
            //send mail
            $dataMail = [
                'email' => $user->email,
                'password' => $request->password
            ];
            $email = new CreateUserMail($dataMail);
            Mail::to($user->email)->send($email);
            return redirect()->route('users.index')->withFlashSuccess('Successful added user to database.');
        } else {
            return redirect()->back()->withFlashDanger('Server error. Please try again.');
        }
        }catch(\Exception $e){
            return redirect()->back()->withFlashDanger('Server error. Please try again.');
        }
    }

    public function edit ($id)
    {
        $user = User::find($id);
        
        if($user instanceof User){
            return view('admins.user.edit',compact('user'));
        }
        return redirect()->route('users.index')->withFlashDanger('Not found!');
    }

    public function update(UpdateUserRequest $request,$id){
    try{
        $user = User::find($id);

        if($user instanceof User){
            $data = $request->except('password','active','is_admin');
            $data['active'] = $request->filled('active') ? 1 : 0; 
            $data['is_admin'] = $request->filled('is_admin') ? 1 : 0; 
            if($request->password){
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
            if($user instanceof User){
                if($request->password){
                    //send mail
                    $dataMail = [
                        'email' => $user->email,
                        'password' => $request->password
                    ];
                    $email = new UpdatePassWordUserMail($dataMail);
                    Mail::to($user->email)->send($email);
                }
                return redirect()->route('users.index')->withFlashSuccess('Successful updated user to database.');
            }
        }
        return redirect()->route('users.index')->withFlashDanger('Not found!');
    }catch(\Exception $e){
        return redirect()->back()->withFlashDanger('Server error. Please try again.');
    }
    }

    public function active($id)
    {
        $user = User::find($id);
        if ($user && !$user->id === 1) {
            $user->active = !$user->active;
            $user->save();
            return redirect()->back()->with('status', 'success')->with('message', __('User status changed.'));
        }
        return redirect()->back()->with('status', 'error')->with('message', __('Server error. Please try again.'));
    }

    public function destroy($id)
    {   
        try{
            $user = User::find($id);
            if ($user instanceof User && !($user->id === 1)) {
                $user->delete();
                return redirect()->route('users.index')->withFlashSuccess('Successful deleted user to database.');
            }
            return redirect()->route('users.index')->withFlashDanger('Server error. Please try again.');
        }catch(\Exception $e){
            return redirect()->route('users.index')->withFlashDanger('Server error. Please try again.');
        }
    }

    public function ajaxGetUsers(Request $request)
    {
        $eloquent = User::query();
        return DataTables::eloquent($eloquent)
            ->editColumn('role', function ($user) {
                return '<span class="badge badge-primary py-1 px-2">'.$user->role.'</span>';
            })
            ->editColumn('active', function ($user) {
                return '<input disabled type="checkbox" name="active" class="form-check-inline"' . ($user->active ? ' checked' : '') . '>';
            })
            ->editColumn('is_admin', function ($user) {
                return '<input disabled type="checkbox" name="active" class="form-check-inline"' . ($user->is_admin ? ' checked' : '') . '>';
            })
            ->addColumn('Action', function ($user) {
                
                if ($user->id != 1) {
                    if(!$user->is_admin || auth()->id() == 1){
                        $btn = '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-success mr-2"><i class="fas fa-user-edit"></i></a>';
                        $btn .= '<a href="#" data-url="' . route('users.destroy', $user->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                        return $btn;
                    }
                }
                return '';
            })
            ->rawColumns(['Action', 'role', 'is_admin' , 'active'])
            ->toJson();
    }
}
