@php
$dir = explode('/', $volume->path);
array_splice($dir,0,1);
$dir = '/'.implode('/',$dir).'/'.config('lfm.vol.old');
@endphp
@extends('layouts.app')
@section('title','OLD FOLDER')
@section('header_page','Old Folder')

@push('head')

@endpush

@push('script')
    
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                {{-- <form action="{{route('pages.storeRaw',['volume' => $volume->id])}}" method="POST"> --}}
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title text-capitalize text-muted text-monospace text-sm"><i class="fas fa-folder-open text-primary"></i>  {{$dir}}</h6>
                            <div class="ml-auto">
                                <a href="{{route('volumes.detail',['id' =>  $volume->id])}}" type="submit" class="btn btn-link text-muted text-monospace"><i class="fas fa-arrow-circle-left text-danger"></i> Back</a>
                            </div>
                        </div>
                        <div class="card-body">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-12">
                                        <iframe src="/manga/laravel-filemanager?dir={{urldecode($dir)}}"
                                        style="width: 100%; height: 70vh; overflow: hidden; border: none;"></iframe>
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
@endsection