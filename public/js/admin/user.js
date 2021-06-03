$(document).ready(function(){
    let userTable = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
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
            {data: 'id', name: 'id', class: 'mw-50 text-truncate'},
            {data: 'name', name: 'name', class: 'mw-160 text-truncate'},
            {data: 'last_login', name: 'last_login', class: 'mw-160 text-truncate'},
            {data: 'active', name: 'active', class: 'mw-160 text-truncate'},
            {data: 'Action', name: 'Action', class: 'mw-160 text-truncate'},
        ],
        columnDefs: [
            {targets: 4, searchable: false, orderable: false},
        ],
    });

    $('html body').on('click', '.delete', function(event){
        event.preventDefault();
        $.ajax({
            url: $(this).attr('data-url'),
            method: 'delete',
            headers: {
              'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function(res){
                if(res.message && res.message === 'success'){
                    toastr.success('The record has been deleted.');
                    userTable.ajax.reload();
                }
                else{
                    toastr.error('Server error. Try again later.');
                }
            },
            error: function(){
                toastr.error('Server error. Try again later.');
            },
        });
    });


});