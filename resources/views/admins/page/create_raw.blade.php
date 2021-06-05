@php
$dir = explode('/', $volume->path);
array_splice($dir,0,1);
$dir = '/'.implode('/',$dir).'/'.config('lfm.volume.raw');
@endphp
@extends('layouts.app')
@section('title','CREATE RAW')
@section('header_page','Create Raw')

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
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="{{route('pages.storeRaw',['volume' => $volume->id])}}" method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">{{$dir}}</h6>
                        </div>
                        <div class="card-body">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-12">
                                        <iframe src="/laravel-filemanager?dir={{urldecode($dir)}}"
                                        style="width: 100%; height: 70vh; overflow: hidden; border: none;"></iframe>
                                    </div>
                                </div>
                            
                        </div>
                        <div class="card-footer d-flex">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="{{route('volumes.index')}}" class="btn btn-danger ml-auto">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection