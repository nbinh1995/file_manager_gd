$(document).ready(function(){
    var fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        return $.ajax({
            data: data,
            dataType: 'json',
            url: ajaxUrl,
            method: method,
            beforeSend: beforeSend,
        });
    };
    let volumeTable = $('#volume-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[1, 'asc']],
        bAutoWidth: false,
        ajax: {
            url: url_volume_table,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        },
        columns: [
            {data: 'id', class: 'mw-50 text-truncate',searchable: false},
            {data: 'filename', class: 'mw-160 text-truncate'},
            {data: 'bookname',class: 'mw-160 text-truncate'},
            {data: 'status', class: 'mw-160 text-truncate',searchable: false},
            {data: 'Action',  class: 'mw-160 text-truncate'},
            {data: 'is_hide',class: 'mw-160 text-truncate',searchable: false},
        ],
        columnDefs: [
            {targets: 4, searchable: false, orderable: false},
        ],
    });

    $(document).on('click','.is_hide',function(e){
        console.log($(this).is(':checked'));
        var hide = $(this).is(':checked') ? 1 : 0;
        fetchData({hide:hide,id:$(this).val()},url_ajax_hide).done(function(data){
            switch(true){
                case (data.code == 200):
                    toastr.success("Update success!");
                break;
                case (data.code == 404):
                    toastr.warning("Not Found!");
                break;
                default:
                    toastr.error("There were errors. Please try again.");
            }
        }).fail(function(){
            toastr.error("There were errors. Please try again.");
        });;
    });
    $('html body').on('click', '.delete', function(event){
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            icon: 'warning',
        }).then((result) => {
            if (result.value) {
                $("#volume-delete").attr('action',$(this).attr('data-url'));
                $('#password-again-volume').modal('show');
            }
        });
    });
});