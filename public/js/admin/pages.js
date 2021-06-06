$(document).ready(function(event){
    var pageTable = $('#pages-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[1, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: '/ajax/ajaxGetPages',
            method: 'post',
            data:{
                volume:volume_id_page
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            dataSrc:function ( json ) {
                if(hasDownload){
                    hasDownload = false;
                    $('#page-download').trigger('submit');
                } 
                return json.data;
            }
        },
        columns: [
            {data: 'id'},
            {data: 'filename'},
            {data: 'raw'},
            {data: 'clean'},
            {data: 'type'},
            {data: 'sfx'},
            {data: 'check'},
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
                $("#page-delete").attr('action',$(this).attr('data-url'));
                $("#page-delete").trigger('submit');
            }
        });
    });
    checkRoleTask('raw');
    checkRoleTask('clean');
    checkRoleTask('type');
    checkRoleTask('sfx');
    checkRoleTask('check');
    
    function checkRoleTask(type){
        var task_id = '.'+type+'-task-id';
        $(document).on('click',task_id,function(e){
            var role = sessionStorage.getItem('authRole').toLowerCase();
            if(role !== type){
                e.preventDefault();
                e.stopPropagation();
                toastr.warning("The User's Role is not '"+type+"'")
            }else{
                if($(task_id+':checked').length > 0){
                    $('#receive-box').show();
                }else{
                    $('#receive-box').hide();
                }
            }
        })
    }

    checkRoleFolder('raw');
    checkRoleFolder('clean');
    checkRoleFolder('type');
    checkRoleFolder('sfx');
    checkRoleFolder('check');

    function checkRoleFolder(type){
        var task_id = '#'+type+'-folder';
        $(document).on('click',task_id,function(e){
            var role = sessionStorage.getItem('authRole').toLowerCase();
            if(role !== type){
                e.preventDefault();
                e.stopPropagation();
                toastr.warning("The User's Role is not '"+type+"'")
            }
        })
    }
    $(document).on('click','#task-btn',function(e){
        e.preventDefault();
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var task_id = '.'+role+'-task-id:checked';
        var arrayIDTask = [];
        $(task_id).each(function(key, ele){
            arrayIDTask.push($(ele).val());
        })
        
        $('#page-task [name=id_tasks]').val(arrayIDTask.join(','));
        $('#page-task [name=type_task]').val(role);

        $('#page-task').trigger('submit');
    });
});