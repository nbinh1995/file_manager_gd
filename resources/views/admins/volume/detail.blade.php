@extends('layouts.app')

@section('title', __('PAGES'))
@section('header_page', __('Pages'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{route('pages.createOld',['volume' => $volume])}}" class="btn btn-warning btn-xs mb-1" id="old-folder">
                                        <i class="fas fa-folder-open mr-2"></i>{{__('Old Folder')}}</a>
                                    <a href="{{route('pages.createRaw',['volume' => $volume])}}" class="btn btn-secondary btn-xs mb-1" id="raw-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Raw Folder')}}</a>
                                    <a href="{{route('pages.createClean',['volume' => $volume])}}" class="btn btn-primary btn-xs mb-1" id="clean-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Clean Folder')}}</a>
                                    <a href="{{route('pages.createType',['volume' => $volume])}}" class="btn btn-info btn-xs mb-1" id="type-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Type Folder')}}</a>
                                    <a href="{{route('pages.createSFX',['volume' => $volume])}}" class="btn btn-danger btn-xs mb-1" id="sfx-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('SFX Folder')}}</a>
                                    <a href="{{route('pages.createCheck',['volume' => $volume])}}" class="btn btn-success btn-xs mb-1" id="check-folder"><i
                                                class="fas fa-folder-open mr-2"></i>{{__('Check Folder')}}</a>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="pages-table" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>File Name</th>
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
    </div>
    <form action=""  id="page-delete" method="POST">
        {{ csrf_field() }}
        {{ method_field('delete') }}
    </form>
    @if(session()->has('path_download'))
    <form action="{{route('pages.downloadFile')}}"  id="page-download">
        <input type="text" name="path_download" hidden value="{{session('path_download')}}">
    </form>
    @endif
    <div style="position: fixed; right:0; bottom: 20%; display:none" id="receive-box">
        <form action="{{route('pages.addTask',['idVolume' => $volume])}}"  id="page-task" method="POST"  style="height: 50px;width: 90px;background-color: #69696969;display: flex;align-items: center;padding-left: 10px;">
            {{ csrf_field() }}
            <input type="text" hidden name="type_task">
            <input type="text" hidden name="id_tasks">
            <button type="submit" class="btn btn-sm btn-primary mr-2" id="task-btn"><i class="fas fa-upload"></i></button>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{asset('/AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script>
        var volume_id_page = {{$volume}};
        var url_page_table = "{{route('ajaxGetPages')}}";
        var hasDownload = false;
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
@endpush
