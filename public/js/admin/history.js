$(document).ready(function() {
    $('input[data-datepicker]').inputmask({
        mask: '9999-99-99',
        alias: 'date',
        placeholder: 'yyyy-mm-dd',
        insertMode: false,
    }).datepicker({dateFormat: 'yy-mm-dd',});
    $('#select2').select2({
            theme: 'bootstrap4',
            placeholder: "Please select ...",
            allowClear: true
        }
    );
    var historiesTable = $('#logs-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        destroy: true,
        order: [[0, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: url_history_table,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data:{
                user_id:user_id,
                firstTime:first_time,
                lastTime:last_time
            },
        },
        columns: [
            {data: 'created_at',searchable: false},
            {data: 'user_id',searchable: false},
            {data: 'book',searchable: false},
            {data: 'volume' ,searchable: false},
            {data: 'page',searchable: false},
            {data: 'type',searchable: false},
        ],
    });
})