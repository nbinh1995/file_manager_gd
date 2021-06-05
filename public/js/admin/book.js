$(document).ready(function(){
    let userTable = $('#book-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[0, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: 'ajax/ajaxGetBooks',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        },
        columns: [
            {data: 'id',  class: 'mw-50 text-truncate'},
            {data: 'filename', class: 'mw-160 text-truncate'},
            {data: 'Action', class: 'mw-160 text-truncate'},
        ],
        columnDefs: [
            {targets: 2, searchable: false, orderable: false},
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
                $("#book-delete").attr('action',$(this).attr('data-url'));
                $("#book-delete").trigger('submit');
            }
        });
    });


});