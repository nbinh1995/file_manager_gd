@extends('layouts.app')

@section('title', __('Change Password'))
@section('header_page', __('Change Password'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('users.changePassword')}}" method="post">
                        {{csrf_field()}}
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="old-password">{{__('Old Password')}}</label>
                                                <input type="password" class="form-control" id="old-password"
                                                       name="old-password" autofocus>
                                                @if($errors->has('old'))
                                                    <label class="text-danger">{{$errors->get('old')[0]}}</label>
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
    <script src="{{asset('/js/admin/password.js')}}"></script>
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
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@endpush
