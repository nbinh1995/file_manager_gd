@extends('layouts.app')
@section('title','CREATE VOLUME')
@section('header_page','Create Volume')

@push('head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('AdminLTE/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('/js/admin/create_volume.js')}}"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="{{route('volumes.store')}}" method="POST">
                    <div class="card">
                        <div class="card-body">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label >{{__('Please Choose Book:')}}</label>
                                                <select name="book_id"  class="form-control" id="select2">
                                                    <option value=""></option>
                                                    @foreach ($books as $book)
                                                        <option value="{{$book->id}}" {{old('book_id') == $book->id ? 'selected' : ''}}>{{$book->filename}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        @if($errors->has('book_id'))
                                        <label class="text-danger">{{$errors->get('book_id')[0]}}</label>
                                        @endif
                                        <div class="form-group">
                                            <label >{{__('Volume Name')}}</label>
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
                            <a href="{{route('volumes.index')}}" class="btn btn-danger ml-auto">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection