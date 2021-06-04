$(document).ready(function(){
    let volumeTable = $('#volume-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[0, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: 'ajax/ajaxGetVolumes',
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        },
        columns: [
            {data: 'id', class: 'mw-50 text-truncate'},
            {data: 'filename', class: 'mw-160 text-truncate'},
            {data: 'bookname',class: 'mw-160 text-truncate'},
            {data: 'active', class: 'mw-160 text-truncate'},
            {data: 'Action',  class: 'mw-160 text-truncate'},
        ],
        columnDefs: [
            {targets: 4, searchable: false, orderable: false},
        ],
    });

});