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
                                                <input type="text" class="form-control" id="name" name="name" value="{{old('name') ?? ''}}" autofocus>
                                                @if($errors->has('name'))
                                                    <label class="text-danger">{{$errors->get('name')[0]}}</label>
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
                                                <div class="custom-control custom-switch">
                                                    <input name="active" type="checkbox" class="custom-control-input"
                                                           id="active" @isset($old['active']) checked @endisset>
                                                    <label class="custom-control-label"
                                                           for="active">{{__('Active?')}}</label>
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
    <script src="{{asset('/AdminLTE/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('/js/admin/create_user.js')}}"></script>
    @if(session()->has('status'))
        <script>
            @if(session()->get('status') === 'success')
            toastr.success("{{session()->get('message')}}");
            @else
            toastr.error("{{session()->get('message')}}");
            @endif
        </script>
    @endif
@endpush

@push('head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/toastr/toastr.min.css')}}">
@endpush
