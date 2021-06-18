@extends('layouts.app')

@section('title', __('BOOKS'))
@section('header_page', __('Books'))
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{route('books.create')}}" class="btn btn-primary"><i
                                                class="fas fa-plus-circle mr-2"></i>{{__('Add Book')}}</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="book-table" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Book Name</th>
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
    <div class="modal fade" id="password-again-book">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-lightblue">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action=""  id="book-delete" method="POST">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <div class="input-group">
                            <input name="password" type="password" id="password" class="form-control" placeholder="{{ __('Password') }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"  id="show_pw" style="cursor: pointer"></span>
                                    <span class="fas fa-unlock" id="hide_pw" style="display: none;cursor: pointer"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('/AdminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('AdminLTE/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script>
        var url_book_table = "{{route('ajaxGetBooks')}}";
        var show_pw = document.getElementById('show_pw');
        var hide_pw = document.getElementById('hide_pw');
        var pw = document.getElementById('password');
        show_pw.addEventListener('click',function(){
            pw.type = 'text';
            this.style.display = 'none';
            hide_pw.style.display = 'block';
        })

        hide_pw.addEventListener('click',function(){
            pw.type = 'password';
            this.style.display = 'none';
            show_pw.style.display = 'block';
        })
    </script>
    <script src="{{asset('/js/admin/book.js')}}"></script>
@endpush

@push('head')
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('AdminLTE/plugins/sweetalert2/sweetalert2.min.css')}}">
@endpush
