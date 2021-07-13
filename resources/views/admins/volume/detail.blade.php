@extends('layouts.app')

@section('title', __('PAGES'))
@section('header_page', __($volume->book->filename.' - '.$volume->filename))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{route('pages.createOld',['volume' => $volume->id])}}" class="btn btn-warning btn-xs mb-1" id="old-folder">
                                        <i class="fas fa-folder-open mr-2"></i>{{__('Reference Folder')}}</a>
                                    <a href="{{route('pages.createRaw',['volume' => $volume->id])}}" class="btn btn-secondary btn-xs mb-1" id="raw-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Raw Folder')}}</a>
                                    @if ($volume->status === 'pending')
                                    <a href="{{route('pages.createClean',['volume' => $volume->id])}}" class="btn btn-primary btn-xs mb-1" id="clean-folder"><i
                                        class="fas fa-folder-open mr-2"></i>{{__('Clean Folder')}}</a>
                                    <a href="{{route('pages.createType',['volume' => $volume->id])}}" class="btn btn-info btn-xs mb-1" id="type-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Type Folder')}}</a>
                                    <a href="{{route('pages.createSFX',['volume' => $volume->id])}}" class="btn btn-danger btn-xs mb-1" id="sfx-folder"><i
                                        class="fas fa-folder-open mr-2"></i>{{__('SFX Folder')}}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive" id="container-pages-table">
                                        <table id="pages-table" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th style="width: 10px;">
                                                    <select name="type_down" id="type_down" class="border" style="outline: none">
                                                        @if (auth()->user()->is_admin)
                                                            @foreach (config('lfm.volume') as $item)
                                                                @if ($volume->status === 'pending')
                                                                <option value="{{$item}}" >{{$item}}</option>
                                                                @endif
                                                                @if ($volume->status !== 'pending' && ($item === 'Raw' || $item === 'Check'))
                                                                <option value="{{$item}}" >{{$item}}</option>
                                                                @endif
                                                            @endforeach 
                                                        @endif
                                                    </select>
                                                </th>
                                                <th style="width: 30px">File Name</th>
                                                <th>Raw</th>
                                                <th>Clean</th>
                                                <th>Type</th>
                                                <th>SFX</th>
                                                <th>Check</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
        <div class="modal fade" id="modal-show-images">
            <div class="modal-dialog">
                <div class="modal-content"  style="max-height:100vh;">
                    <div class="box-tool-zoom">
                        <div class="zoom-in">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        <div class="zoom-out">
                            <i class="fas fa-search-minus"></i>
                        </div>
                    </div>
                    <div class="modal-header bg-lightblue">
                        <h6 class="modal-title text-uppercase " id="title-show-image">ID ...</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" style="overflow: auto">
                        <div id="skeleton">
                            <div class="row">
                                <div class="col-12  skeleton-block" >

                                </div>
                            </div>
                        </div>
                        {{-- <img src="" alt="" id="image-page-show" > --}}
                        <img src="" alt="" class="image-page current">
                        <img src="" alt="" class="image-page prev"  >
                        <img src="" alt="" class="image-page next" >
                        {{-- <img src="" alt="" class="image-page tmp"> --}}
                    </div>
                    <div class="modal-footer justify-content-between" id="action-check" style="display: none">
                        <button type="button" class="btn btn-secondary close-check" data-dismiss="modal">Close</button>
                        <div>
                            <button type="button" class="btn btn-warning reject-check">Reject</button>
                            <button type="button" class="btn btn-success done-check">Done</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <div class="image-arrow left" data-url="{{route('file-manager.bufferImage')}}"><i class="fas fa-chevron-left"></i></div>
        <div class="image-arrow right" data-url="{{route('file-manager.bufferImage')}}"><i class="fas fa-chevron-right"></i></div>

        <div class="modal fade" id="modal-note-page">
            <div class="modal-dialog">
                <form action="{{route('pages.rejectCheck')}}" id="reject-check-form" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-content">
                            <input type="text" name="volume_id" value="{{$volume->id}}" hidden>
                            <input type="text" name="fileName" hidden>
                            <div class="modal-body">
                                <div class="form-group">
                                    <h4 class="text-monospace" >{{__('Note')}}</h4>
                                    <div id="dropZ">
                                        <div class="container-nip">
                                            <div id="note_image_preview">
                                            </div>
                                        </div>
                                        <textarea name="note" cols="30" rows="6" class="form-control" id="note_textarea"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="note_image_page" class="text-monospace text-muted" style="cursor: pointer"><i class="fas fa-paperclip"></i> Attach<i id="name_note_image"></i></label>
                                    <input type="file" name="note_image" id="note_image_page" style="display: none" >
                                </div>
                                
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-secondary close-check" data-dismiss="modal">Close</button>
                                <div>
                                    <button type="submit" class="btn btn-success reject-check-submit">Submit</button>
                                </div>
                            </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    @if(session()->has('path_download'))
    <form action="{{route('pages.downloadFile')}}"  id="page-download">
        <input type="text" name="path_download" hidden value="{{session('path_download')}}">
    </form>
    @endif
    <form action=""  id="page-delete" method="POST">
        {{ csrf_field() }}
        {{ method_field('delete') }}
    </form>
    <div style="position: fixed; right:0; bottom: 28%; display:none;z-index: 9999;" id="download-box">
        <form action="{{route('pages.downTask',['idVolume' => $volume->id])}}"  id="download-page-task" method="POST"  style="height: 50px;width: 130px;background-color: #69696969;display: flex;align-items: center;padding-left: 10px;">
            {{ csrf_field() }}
            <input type="text" hidden name="id_tasks"> 
            <input type="text" hidden name="type_task" >
            <button type="submit" class="btn btn-sm btn-primary mr-2" id="download-task-btn" title="Get Task"><i class="fas fa-download"></i></button><span class="text-monospace text-get-task">Download File</span>
        </form>
    </div>
    <div style="position: fixed; right:0; bottom: 20%; display:none;z-index: 9999;" id="receive-box">
        <form action="{{route('pages.addTask',['idVolume' => $volume->id])}}"  id="page-task" method="POST"  style="height: 50px;width: 130px;background-color: #69696969;display: flex;align-items: center;padding-left: 10px;">
            {{ csrf_field() }}
            <input type="text" hidden name="type_task" >
            <input type="text" hidden name="id_tasks">
            <button type="submit" class="btn btn-sm btn-warning mr-2" id="task-btn" title="Get Task"><i class="fas fa-shopping-basket"></i></button><span class="text-monospace text-get-task">Get Task</span>
        </form>
    </div>
    <div style="position: fixed; right:0; bottom: 12%; display:none;z-index: 9999;" id="undo-box">
        <form action="{{route('pages.undoTask',['idVolume' => $volume->id])}}"  id="undo-page-task" method="POST"  style="height: 50px;width: 130px;background-color: #69696969;display: flex;align-items: center;padding-left: 10px;">
            {{ csrf_field() }}
            <input type="text" hidden name="type_task" >
            <input type="text" hidden name="id_tasks">
            <button type="submit" class="btn btn-sm btn-info mr-2" id="undo-task-btn" title="Get Again"><i class="fas fa-undo-alt"></i></button><span class="text-monospace text-get-task">Get Again</span>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{asset('/AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script>
        var pending = "{{$volume->status === 'pending' ? '1' : '0'}}";
        var check = "{{auth()->user()->is_admin ? '1' : '0'}}";
        var volume_id_page = {{$volume->id}};
        var url_page_table = "{{route('ajaxGetPages')}}";
        var hasDownload = false;
        var url_buffer_image = "{{route('file-manager.bufferImage')}}";
        // var url_check_process = "{{route('pages.checkProcessZip')}}";
        var url_done_check = "{{route('pages.doneCheck')}}";
        var x, y;
        $('#modal-show-images .modal-body').on('mousedown', function(e) {
            e.preventDefault();
            if (e.type == "mousedown") {
                var x ,y;
                $(this).on('mousemove',function(event){
                    if (x && y) {
                        // Scroll window by difference between current and previous positions
                        this.scrollBy(x - event.clientX,y - event.clientY);
                    }
                    x= event.clientX;
                    y= event.clientY;
                })
            }
        });
        $(document).on('click','.note_image_reject',function(){
            var url_tab = $(this).attr('src');
            window.open(url_tab, '_blank').focus();
        })
        $(document).on('mouseup', function(e) {
            e.preventDefault();
            if (e.type == "mouseup") {
                $('#modal-show-images .modal-body').unbind('mousemove');
            }
        });
    </script>
    @if (session()->has('path_download'))
    <script>
        hasDownload = true;
    </script>
    @endif
    <script src="{{asset('/js/admin/pages.js')}}"></script>

@endpush

@push('head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/sweetalert2/sweetalert2.min.css')}}">
    <style>
        /* .popover .popover-body{
            overflow: auto;
            max-height: 200px;
        } */
        #note_textarea{
            border: none;
        }
        #dropZ{
            padding: 20px;
            box-shadow: inset 0px 0px 19px 8px rgb(0 0 0 / 27%);
        }

        .note_image_reject{
            width: 200px;
            height: auto;
            cursor: pointer;
        }
        .container-nip{
            display: none;
            padding: 10px;
        }
        #note_image_preview{
            width: 100%;
            height: 200px;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            box-shadow: 0px 0px 10px 0px rgb(0 0 0 / 20%);
        }
        #modal-show-images img{
            cursor:move;
        }

        .box-tool-zoom{
            position: absolute;
            right: 100%;
            top: 55px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 5px;
            z-index: 99999;
            color: white;
            font-size: 20px;
        }
        .zoom-in{
            cursor: zoom-in;
            padding: 0px 5px;
        }
        .zoom-out{
            cursor: zoom-out;
            padding: 0px 5px;
        }
        #image-page-show {
            width: 100%; 
            height:auto
        }
        .image-page {
            display:none; 
            width: 100%; 
            height:auto
        }
        .current{
            display: block;
        }
        @media screen and (max-width: 767px){
            .box-tool-zoom{
            display: flex;
            right: 0;
            color: black;
            background-color: rgba(0, 0, 0, 0.4);
            }
        }
        @media screen and (min-width: 767px){
            #container-pages-table #pages-table_wrapper .row:last-child{
            flex-direction: column;
            }

            #container-pages-table #pages-table_wrapper .row:last-child div:last-child{
                padding: 0;
            }

            #container-pages-table #pages-table_wrapper .row:last-child div:last-child .pagination{
                justify-content: flex-start;
            }
        }

        .toast-center{
            top: 50% !important;
            transform: translateY(-50%);
        }
        #pages-table td {
            padding: 0.25rem 0 0.25rem 0.5rem;
            vertical-align: middle;
        }

        #pages-table td label{
            margin-bottom: 0;
        }

        #skeleton{
            display: none;
            overflow: hidden; 
            background-color: #e6e6e6;
            border: 1px solid #e6e6e6;
            border-radius: 5px; 
        }
        .skeleton-block{
            height: 90vh;
            -webkit-animation: phAnimation 0.8s linear infinite;
            animation: phAnimation 0.8s linear infinite;
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 46%, rgba(255, 255, 255, 0.35) 50%, rgba(255, 255, 255, 0) 54%) 50% 50%;
            
        }
        @-webkit-keyframes phAnimation {
        0% {
            transform: translate3d(-30%, 0, 0); }
        100% {
            transform: translate3d(30%, 0, 0); } }

        @keyframes phAnimation {
        0% {
            transform: translate3d(-30%, 0, 0); }
        100% {
            transform: translate3d(30%, 0, 0); } }
        #task-btn + span  {
        animation: blinker 1s linear infinite;
        }
        #download-task-btn + span  {
        animation: blinker 1s linear infinite;
        }
        #undo-task-btn + span  {
        animation: blinker 1s linear infinite;
        }
        .text-get-task{
            background: -webkit-linear-gradient(#dc3545 , #ffc107);
            -webkit-text-stroke: 1px #343a40;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bolder;
        }
        @keyframes blinker {
        50% {
            opacity: 0;
        }
        }
    </style>
@endpush
