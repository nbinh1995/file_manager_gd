@extends('layouts.app')
@section('title','EDIT VOLUME')
@section('header_page','Edit Volume')

@push('head')
@endpush

@push('script')
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="{{route('volumes.update',['id' => $volume->id])}}" method="POST">
                    <div class="card">
                        <div class="card-body">
                                {{csrf_field()}}
                                {{ method_field('patch') }}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label >{{__('Trạng Thái')}}</label>
                                                <select name="status"  class="form-control" id="select2">
                                                    @foreach (config('lfm.status_vol') as $key => $status)
                                                        <option value="{{$key}}" {{old('status',$volume->status) == $key ? 'selected' : ''}}>{{$status}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        @if($errors->has('status'))
                                        <label class="text-danger">{{$errors->get('status')[0]}}</label>
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