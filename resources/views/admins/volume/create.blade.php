@extends('layouts.app')
@section('title','CREATE VOLUME')
@section('header_page','Create Volume')

@push('head')
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <!-- Toastr -->
    <script src="{{ asset('AdminLTE/plugins/toastr/toastr.min.js')}}"></script>
    <!-- BootBox -->
    {{-- <script src="{{ asset('AdminLTE/plugins/bootbox/bootbox.js')}}"></script> --}}
    <script src="{{asset('AdminLTE/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('AdminLTE/plugins/axios/dist/axios.min.js')}}"></script>
    <script src="{{asset('vendor/laravel-filemanager/js/stand-alone-button.js')}}"></script>
    <script src="{{asset('/js/admin/create_volume.js')}}"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('volumes.store')}}"  enctype="multipart/form-data" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >{{__('Chọn đường dẫn thư mục cần tạo')}}</label>
                                            <select name="book_id"  class="form-control" id="select2">
                                                <option value=""></option>
                                                @foreach ($books as $book)
                                                    <option value="{{$book->id}}">{{$book->filename}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <label >{{__('Tên thư mục được')}}</label>
                                        <input type="text" name="filename" class="form-control" placeholder="Folder Name">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{route('volumes.index')}}" class="btn btn-danger ml-auto">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection