@extends('layouts.app')

@section('title', __('Create User'))
@section('header_page', __('Create User'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('users.store')}}" method="post">
                        {{csrf_field()}}
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12">

                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">{{__('Name')}}</label>
                                                <input type="text" class="form-control" id="name" name="username" value="{{old('username') ?? ''}}" autofocus>
                                                @if($errors->has('username'))
                                                    <label class="text-danger">{{$errors->get('username')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="email">{{__('Email')}}</label>
                                                <input type="text" class="form-control" id="email" name="email" value="{{old('email') ?? ''}}" autofocus>
                                                @if($errors->has('email'))
                                                    <label class="text-danger">{{$errors->get('email')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="password">{{__('Password')}}</label>
                                                <input type="password" class="form-control" id="password"
                                                        name="password">
                                                @if($errors->has('password'))
                                                    <label class="text-danger">{{$errors->get('password')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="password_confirmation">{{__('Password Confirmation')}}</label>
                                                <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation">
                                                @if($errors->has('password_confirmation'))
                                                    <label class="text-danger">{{$errors->get('password_confirmation')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="role_multi">{{__('Roles')}}</label>
                                                <div id="role_multi" class="d-flex justify-content-between">
                                                    @foreach (config('lfm.volume') as $item)
                                                    <label class="text-muted text-monospace"><input type="checkbox" name="role_multi[]" value="{{$item}}" {{in_array($item,old('role_multi',[])) ? 'checked' : ''}} > {{$item}}</label>
                                                    @endforeach
                                                </div>
                                                @if($errors->has('role_multi[]'))
                                                    <label class="text-danger">{{$errors->get('role')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="role">{{__('Default Role')}}</label>
                                                <select class="form-control" name="role" id="role" >
                                                    @foreach (old('role_multi',[]) as $item)
                                                        <option value="{{$item}}" {{old('role',$user->role) === $item ? 'selected' : ''}}>{{$item}}</option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('role'))
                                                    <label class="text-danger">{{$errors->get('role')[0]}}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input name="is_admin" type="checkbox" class="custom-control-input"
                                                        id="is_admin" @isset($old['is_admin']) checked @endisset>
                                                    <label class="custom-control-label"
                                                        for="is_admin">{{__('Admin')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                                    <a href="{{route('users.index')}}" class="btn btn-danger">Back</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

@endsection

@push('script')
    {{-- <script src="{{asset('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script> --}}
	<script src="{{asset('/js/admin/create_user.js')}}"></script>
@endpush

