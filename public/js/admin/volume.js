$(document).ready(function(){
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
            {data: 'id', class: 'mw-50 text-truncate'},
            {data: 'filename', class: 'mw-160 text-truncate'},
            {data: 'bookname',class: 'mw-160 text-truncate'},
            {data: 'status', class: 'mw-160 text-truncate'},
            {data: 'Action',  class: 'mw-160 text-truncate'},
        ],
        columnDefs: [
            {targets: 4, searchable: false, orderable: false},
        ],
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