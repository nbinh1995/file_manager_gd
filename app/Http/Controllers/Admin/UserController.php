<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function index()
    {
        return view('admins.user.index', ['active' => 8]);
    }

    public function create()
    {
        return view('admins.user.create', ['active' => 8]);
    }

    public function password()
    {
        return view('admins.user.password', ['active' => 8]);
    }

    public function changePassword(PasswordRequest $request)
    {
        if (Hash::check($request->get('old-password'), Auth::user()->password)) {
            $user = User::findOrFail(Auth::user()->id);
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->back()->with('status', 'success')->with('message', __('Your password successful updated.'));
        } else {
            $errors = new MessageBag();
            return redirect()->back()->withErrors($errors->add('old', __('Your old password not correct.')));
        }
    }

    public function store(UserRequest $request)
    {
        $data = $request->only('name', 'email', 'password');
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if ($user) {
            if ($request->filled('active')) {
                $user->active = true;
                $user->save();
            }
            return redirect()->back()->with('status', 'success')->with('message', __('Successful added user to database.'));
        } else {
            return redirect()->back()->with('status', 'error')->with('message', __('Server error. Please try again.'));
        }
    }

    public function active($id)
    {
        $user = User::find($id);
        if ($user && !$user->is_admin) {
            $user->active = !$user->active;
            $user->save();
            return redirect()->back()->with('status', 'success')->with('message', __('User status changed.'));
        }
        return redirect()->back()->with('status', 'error')->with('message', __('Server error. Please try again.'));
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user && !$user->is_admin) {
            $user->delete();
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'error']);
    }

    public function ajaxGetUsers(Request $request)
    {
        $eloquent = User::query();
        return DataTables::eloquent($eloquent)
            ->editColumn('active', function ($user) {
                return '<input disabled type="checkbox" name="active" class="form-check-inline"' . ($user->active ? ' checked' : '') . '>';
            })
            ->addColumn('Action', function ($user) {
                if (!$user->is_admin) {
                    $btn = '<a href="' . route('users.active', $user->id) . '" class="btn btn-sm btn-success mr-2"><i class="fas fa-user-lock"></i></a>';
                    $btn .= '<a href="#" data-url="' . route('users.destroy', $user->id) . '" class="btn btn-sm delete btn-danger"><i class="fas fa-trash"></i></a>';
                    return $btn;
                }
                return '';
            })
            ->rawColumns(['Action', 'active'])
            ->toJson();
    }
}
