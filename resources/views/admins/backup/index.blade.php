@extends('layouts.app')
@section('title','Backup Database')
@section('header_page','Backup File')

@push('head')
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet"
          href="{{ asset('AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/jquery-ui/jquery-ui.min.css')}}">
@endpush

@push('script')
    <!-- datatable  -->
    <script src="{{ asset('AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <!-- Toastr -->
    <script src="{{ asset('AdminLTE/plugins/toastr/toastr.min.js')}}"></script>
    <!-- BootBox -->
    <script src="{{ asset('AdminLTE/plugins/bootbox/bootbox.js')}}"></script>
    <script src="{{asset('js/admin/backup.js')}}"></script>

@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class=" col-sm-12 col-md-2 mb-2">
                                    <button class="btn btn-primary backup"><i class="fas fa-sync-alt mr-2"
                                                                              style="pointer-events: none"></i>{{__('Backup')}}
                                    </button>
                                </div>
                                <div class="col-md-4 col-sm-12 mr-auto mb-2">
                                    <form action="{{route('ajaxUpdateKeepDays')}}" class="form-inline unpaid-form"
                                          method="post">
                                        <div class="input-group w-100">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <label class="text-xs">{{__('Keep day(s):')}}</label>
                                            </span>
                                            </div>
                                            <input id="keep-days" title="{{isset($keep_days) ? $keep_days : ''}}"
                                                   class="form-control keep-days" min="1" type="number" name="keep-days"
                                                   value="{{$keep_days ?? ''}}">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <button title="{{__('Update')}}"
                                                        class="btn btn-info btn-xs update-keep-days"><i
                                                            class="fas fa-save"></i></button>
                                            </span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="common-table" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><input type="checkbox" class="mr-2 check-all"></th>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Modified</th>
                                    <th>Size</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th><input type="checkbox" class="mr-2 check-all"></th>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Modified</th>
                                    <th>Size</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </tfoot>
                            </table>
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
        <!-- multi remove -->
        <div class="multi-remove">
            <button class="btn btn-danger remove-selected shadow-lg mb-2"><i class="fas fa-trash mr-2"
                                                                                     style="pointer-events: none;"></i>{{__('Remove Selected')}}
            </button>
        </div>
        <!-- /.multi remove -->
    </div>
@endsection