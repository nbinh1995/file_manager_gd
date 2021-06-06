@extends('layouts.app')

@section('title', __('FILE MANAGER'))
@section('header_page', __('File Manager'))
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <iframe src="/laravel-filemanager"
                style="width: 100%; height: 80vh; overflow: hidden; border: none;"></iframe>
        </div>
    </div>
</div>
@endsection

@push('script')
@endpush

@push('head')
@endpush
