@extends('layouts.app')
@section('title','CREATE BOOK')
@section('header_page','Create Book')

@push('head')
@endpush

@push('script')
    <script src="{{asset('/js/admin/volume.js')}}"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="{{route('books.store')}}"  enctype="multipart/form-data" method="POST">
                <div class="card">
                    <div class="card-body">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >{{__('Book Name')}}</label>
                                        <input type="text" name="filename" value="{{old('filename')}}" class="form-control" placeholder="Folder Name">
                                    </div>
                                    @if($errors->has('filename'))
                                    <label class="text-danger">{{$errors->get('filename')[0]}}</label>
                                    @endif
                                </div>
                            </div>
                    </div>
                    <div class="card-footer d-flex">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{route('books.index')}}" class="btn btn-danger ml-auto">Back</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection