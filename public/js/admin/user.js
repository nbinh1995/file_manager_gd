$(document).ready(function(){
    let userTable = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[0, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: 'ajax/ajaxGetUsers',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        },
        columns: [
            {data: 'id'},
            {data: 'username', class: 'mw-160 text-truncate'},
            {data: 'email', class: 'mw-160 text-truncate'},
            {data: 'role'},
            {data: 'is_admin'},
            {data: 'active'},
            {data: 'last_login'},
            {data: 'Action'},
        ],
        columnDefs: [
            {targets: 7, searchable: false, orderable: false},
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
                $("#user-delete").attr('action',$(this).attr('data-url'));
                $("#user-delete").trigger('submit');
            }
        });
    });


});