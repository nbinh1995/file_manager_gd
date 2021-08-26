@extends('layouts.app')

@section('title', __('Logs'))
@section('header_page', __('Logs'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @include('admins.history.includes.form_search_histories',['firstTime'=>$firstTime,'lastTime'=>$lastTime,'users'=>$users])
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="logs-table" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>DateTime</th>
                                                <th>UserName</th>
                                                <th>Book</th>
                                                <th>Volume</th>
                                                <th>Page</th>
                                                <th>Folder</th>
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
                        <div class="card-footer">
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection

@push('script')
    <script src="{{asset('/AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/inputmask/jquery.inputmask.bundle.min.js')}}"></script>
    <script src="{{asset('AdminLTE/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script>
        var url_history_table = "{{route('ajaxHistories')}}";
        var first_time = "{{$firstTime}}";
        var last_time = "{{$lastTime}}";
        var user_id = "{{$user_id}}";

    </script>
    <script src="{{asset('/js/admin/history.js')}}"></script>
@endpush

@push('head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.css')}}">
@endpush
