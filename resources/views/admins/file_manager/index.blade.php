@extends('layouts.app')

@section('title', __('FILE MANAGER'))
@section('header_page', __('File Manager'))
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <iframe src="/laravel-filemanager"
                style="width: 100%; height: 100vh; overflow: hidden; border: none;"></iframe>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{asset('/AdminLTE/plugins/toastr/toastr.min.js')}}"></script>
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
