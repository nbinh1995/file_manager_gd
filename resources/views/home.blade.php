@extends('layouts.app')

@section('title', __('HOME'))
@section('header_page', __('Home'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-body">
                        <form action="{{route('goToVolDetail')}}" method="POST">
                            {{csrf_field()}}
                            <div class="row justify-content-md-center">
                                <div class="col-12 col-md-6 col-xl-5">
                                    <div class="form-group">
                                        <label >{{__('Book: ')}}</label>
                                        <select name="book" class="form-control" id="book-home">
                                            <option selected disabled>----/----</option>
                                            @foreach ($books as $book)
                                                <option value="{{$book->id}}">{{$book->filename}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($errors->has('book'))
                                    <label class="text-danger">{{$errors->get('book')[0]}}</label>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label >{{__('Volume: ')}}</label>
                                        <select name="volume" class="form-control" id="volume-home" >
                                            <option selected disabled>----/----</option>
                                            @foreach ($volumes as $volume)
                                            <option value="{{$volume->id}}">{{$volume->filename}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($errors->has('volume'))
                                    <label class="text-danger">{{$errors->get('volume')[0]}}</label>
                                    @endif
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="submit" class="btn btn-primary btn-block">GO</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection

@push('script')
<script>
    var url_get_vol_by_book = "{{route('ajaxGetVolumesByBookID')}}";
</script>
<script src="{{asset('js/admin/home.js')}}"></script>
@endpush

@push('head')
@endpush