$(document).ready(function(event){
    var flagShift = false;
    var flagShow = true;
    var flagReload = false;
    var fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
        return $.ajax({
            data: data,
            dataType: 'json',
            url: ajaxUrl,
            method: method,
            beforeSend: beforeSend,
        });
    };
    var pageTable = $('#pages-table').DataTable({
        processing: true,
        serverSide: true,
        // responsive: true,
        searching: true,
        destroy: true,
        order: [[1, 'desc']],
        bAutoWidth: false,
        ajax: {
            url: url_page_table,
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
    $(document).on('keydown',function(e){
        if(e.keyCode === 16){
            flagShift =true;
        }
        if($('#modal-show-images').is(':visible')){
            if(e.keyCode === 37){
                $('.image-arrow.left:visible').trigger('click');
            }
            if(e.keyCode === 39){
                $('.image-arrow.right:visible').trigger('click');
            }
        }
    });

    $(document).on('keyup',function(e){
        if(e.keyCode === 16){
            flagShift =false;
        }
    });

    checkRoleTask('raw');
    checkRoleTask('clean');
    checkRoleTask('type');
    checkRoleTask('sfx');
    checkRoleTask('check');
    
    function checkRoleTask(type){
        var task_id = '.'+type+'-task-id';
        $(document).on('click',task_id,function(e){
            var current = this;
            var role = sessionStorage.getItem('authRole').toLowerCase();
            if(role !== type){
                e.preventDefault();
                e.stopPropagation();
                toastr.warning("The User's Role is not '"+type+"'")
            }else{
                if($(task_id+':checked').length > 0){
                    $('#receive-box').show();
                    if(flagShift){
                        var firstValue = 0;
                        var lastValue = 0;
                        $(task_id).each(function(index,ele){
                            if($(ele).val() == $(task_id+':checked').first().val()){
                                firstValue = index;
                            }
                            if($(ele).val() == $(current).val()){
                                lastValue = index;
                            }
                        });
                        if(firstValue < lastValue){
                            for(var i = firstValue ; i < lastValue ; i++){
                                $($(task_id)[i]).prop('checked',true);
                            }
                        }else{
                            for(var i = lastValue ; i < firstValue ; i++){
                                $($(task_id)[i]).prop('checked',true);
                            }
                        }
                    }
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
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin"></i>');
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

    showImages('raw');
    showImages('clean');
    showImages('type');
    showImages('sfx');
    showImages('check');

    function showImages(type){
        var folderImage = '.'+type+'-detail';
        $(document).on('click',folderImage,function(e){
            e.preventDefault();
            var role = sessionStorage.getItem('authRole').toLowerCase();
            var url = $(this).data('url');
            $('#modal-show-images').find('img').attr('src',url);
            $('#title-show-image').text('');
            $('#title-show-image').text(type+ ' ID:');
            loading(true);
            if(type === 'sfx' && role === 'check' && $(this).closest('td').next().find('label').text() == 'Pending'){
                $('#modal-show-images').find('img').data('hasAction','1');
            }else{
                $('#modal-show-images').find('img').data('hasAction','0');
            }
            $('#modal-show-images').modal('show');
        })
    }

    $('#modal-show-images').on('show.bs.modal', function (e) {
        $('.image-arrow').show();
        loadingCheck(false);
    })

    $('#modal-show-images').on('hide.bs.modal', function (e) {
        $('.image-arrow').hide();
        $('#action-check').hide();
        if(flagReload){
            pageTable.ajax.reload(null, false);
            flagReload = false;
        }
    })

    $('.image-arrow.left').on('click',function(e){
        if(flagShow){
        var url = $(this).data('url');
        var page_id = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('page_id');
        var type = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('type');

        fetchData({type:type,page_id:page_id,volume_id:volume_id_page},url,'GET',loading(true)).done(function(data){
            if(data.code === 200){
                $('#modal-show-images').find('img').data('hasAction',data.hasAction);
                $('#modal-show-images').find('img').attr('src',data.src);
            }else{
                toastr.warning("Not Found!")
                loading(false);
            }
        }).fail(function(){
            toastr.error("There were errors. Please try again.");
            loading(false);
        });
        }
    });

    $('.image-arrow.right').on('click',function(e){
        if(flagShow){
        var url = $(this).data('url');
        var page_id = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('page_id');
        var type = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('type');

        fetchData({type:type,page_id:page_id,volume_id:volume_id_page},url,'GET',loading(true)).done(function(data){
            if(data.code === 200){
                $('#modal-show-images').find('img').data('hasAction',data.hasAction);
                $('#modal-show-images').find('img').attr('src',data.src);
            }else{
                toastr.warning("Not Found!");
                loading(false);
            }
        }).fail(function(){
            toastr.error("There were errors. Please try again.");
            loading(false);
        });
        }
    });

    function loading(status){
        if(status){
            flagShow = false;
            $('#skeleton').show();
            $('#image-page-show').hide();
        }else{
            flagShow = true;
            $('#skeleton').hide();
            $('#image-page-show').show();
        }
    }

    $('#image-page-show').on('load',function(){
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var page_id = (new URL($(this).attr('src'))).searchParams.get('page_id');
        var textHead = $('#title-show-image').text().split(':');
        $('#title-show-image').text('');
        $('#title-show-image').text(textHead[0]+': '+page_id);
        if($(this).data('hasAction') == 1 && role === 'check'){
            $('#action-check').show();
        }else{
            $('#action-check').hide();
        }
        loadingCheck(false);
        loading(false);
    })

    $('.reject-check').on('click',function(e){
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var page_id = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('page_id');
        var type = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('type');
        if(type === 'SFX' && role === 'check'){
            fetchData({page_id:page_id},url_reject_check,'GET',loadingCheck(true)).done(function(data){
                $('.image-arrow.left:visible').trigger('click');
            }).fail(function(){
                toastr.error("There were errors. Please try again.");
            });
        }else{
            toastr.error("Roles user or file is not correct!");
        }
    })

    $('.done-check').on('click',function(e){
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var page_id = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('page_id');
        var type = (new URL($('#modal-show-images').find('img').attr('src'))).searchParams.get('type');
        if(type === 'SFX' && role === 'check'){
            fetchData({page_id:page_id},url_done_check,'GET',loadingCheck(true)).done(function(data){
                $('.image-arrow.left:visible').trigger('click');
            }).fail(function(){
                toastr.error("There were errors. Please try again.");
            });
        }else{
            toastr.error("Roles user or file is not correct!");
        }
    })

    function loadingCheck(status){
        if(status){
            flagReload = true;
            $('#modal-show-images').find('.close-check').prop('disabled',true);
            $('#modal-show-images').find('.reject-check').prop('disabled',true);
            $('#modal-show-images').find('.done-check').prop('disabled',true);
        }else{
            $('#modal-show-images').find('.close-check').prop('disabled',false);
            $('#modal-show-images').find('.reject-check').prop('disabled',false);
            $('#modal-show-images').find('.done-check').prop('disabled',false);
        }
    }
});