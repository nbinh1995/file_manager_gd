$(document).ready(function(event){
    var flagShift = false;
    var flagShow = true;
    var flagReload = false;
    var blob = '';
    var fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
        return $.ajax({
            data: data,
            dataType: 'json',
            url: ajaxUrl,
            method: method,
            beforeSend: beforeSend,
        });
    };
    function setDownLoadByRole(){
        if(check == 0){
            var typeDown = '';
            switch($('#auth-role').val()){
                case 'Clean':
                    typeDown = 'Raw';
                break;
                case 'Type':
                    typeDown = pending == 1 ? 'Clean' : '';
                break;
                case 'SFX':
                    typeDown = pending == 1 ?  'Type' : '';
                break;
                case 'Check':
                    typeDown = pending == 1 ? 'SFX' : '';
                break;
                default:
                    typeDown = '';
            }
            $('#type_down').html('');
            $('#type_down').html('<option value="'+typeDown+'" selected >'+(typeDown ? typeDown : '#')+'</option>');
            $('#type_down').trigger('change');
        }
    }

    function configDataTable(){
    return $('#pages-table').DataTable({
            processing: true,
            serverSide: true,
            // responsive: true,
            searching: true,
            destroy: true,
            order: [[1, 'asc']],
            bAutoWidth: false,
            lengthMenu: [ 50, 100, 200 ],
            pageLength:200,
            ajax: {
                url: url_page_table,
                method: 'post',
                data:{
                    volume:volume_id_page,
                    type_down: $('#type_down').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            },
            columns: [
                {data: 'id',searchable: false},
                {data: 'filename',orderData:[8]},
                {data: 'raw',searchable: false},
                {data: 'clean',searchable: false},
                {data: 'type',searchable: false},
                {data: 'sfx',searchable: false},
                {data: 'check',searchable: false},
                {data: 'Action'},
                {data: 'id_filename', visible: false,searchable: false},
            ],
            columnDefs: [
                {targets: 0, searchable: false, orderable: false},
                {targets: 7, searchable: false, orderable: false},
            ],
            drawCallback: function( settings ) {
                if(hasDownload){
                    hasDownload = false;
                    $('#page-download').trigger('submit');
                } 
                $('[data-toggle="popover"]').popover(); 
            }
        });
    }

    var pageTable = configDataTable();
    $('#type_down').on('change',function(e){
        e.preventDefault();
        pageTable = configDataTable();
    })
    setDownLoadByRole();
    $('#auth-role').on('change',function(e){
        setDownLoadByRole();
    })
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
        if(e.key === 'Shift'){
            flagShift =true;
        }
        if($('#modal-show-images').is(':visible')){
            if(e.key === 'ArrowLeft'){
                $('.image-arrow.left:visible').trigger('click');
            }
            if(e.key === 'ArrowRight'){
                $('.image-arrow.right:visible').trigger('click');
            }
        }
    });

    $(document).on('keyup',function(e){
        if(e.key === 'Shift'){
            flagShift =false;
        }
    });

    checkRoleTask('raw');
    checkRoleTask('clean');
    checkRoleTask('type');
    checkRoleTask('sfx');
    // checkRoleTask('check');
    
    $(document).on('click','.download-page-id',function(e){
        var current = this;
            if($('.pending-task:checked').length > 0){
                $('.pending-task').prop('checked',false);
                $('#receive-box').hide();
            }
            if($('.doing-task:checked').length > 0){
                $('.doing-task').prop('checked',false);
                $('#undo-box').hide();
            }
            if($('.download-page-id:checked').length > 0){
                $('#download-box').show();
                if(flagShift){
                    var firstValue = 0;
                    var lastValue = 0;
                    $('.download-page-id').each(function(index,ele){
                        if($(ele).val() == $('.download-page-id:checked').first().val()){
                            firstValue = index;
                        }
                        if($(ele).val() == $(current).val()){
                            lastValue = index;
                        }
                    });
                    if(firstValue < lastValue){
                        for(var i = firstValue ; i < lastValue ; i++){
                            $($('.download-page-id')[i]).prop('checked',true);
                        }
                    }else{
                        for(var i = lastValue ; i < firstValue ; i++){
                            $($('.download-page-id')[i]).prop('checked',true);
                        }
                    }
                }
            }else{
                $('#download-box').hide();
            }
    })

    $(document).on('click','#download-task-btn',function(e){
        e.preventDefault();
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin"></i>');
        var role = $('#type_down').val();
        var arrayIDTask = [];
        $('.download-page-id:checked').each(function(key, ele){
            arrayIDTask.push($(ele).val());
        })
        
        $('#download-page-task [name=id_tasks]').val(arrayIDTask.join(','));
        $('#download-page-task [name=type_task]').val(role);

        $('#download-page-task').trigger('submit');
    });

    function checkRoleTask(type){
        var task_id = '.'+type+'-task-id';
        var undo_task_id = '.'+type+'-undo-task';
        $(document).on('click',task_id,function(e){
            var current = this;
            var role = sessionStorage.getItem('authRole').toLowerCase();
            if(role !== type){
                e.preventDefault();
                e.stopPropagation();
                toastr.warning("The User's Role is not '"+type+"'")
            }else{
                if($('.download-page-id:checked').length > 0){
                    $('.download-page-id').prop('checked',false);
                    $('#download-box').hide();
                }
                if($('.doing-task:checked').length > 0){
                    $('.doing-task').prop('checked',false);
                    $('#undo-box').hide();
                }
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
        $(document).on('click',undo_task_id,function(e){
            var current = this;
            var role = sessionStorage.getItem('authRole').toLowerCase();
            if(role !== type){
                e.preventDefault();
                e.stopPropagation();
                toastr.warning("The User's Role is not '"+type+"'")
            }else{
                if($('.download-page-id:checked').length > 0){
                    $('.download-page-id').prop('checked',false);
                    $('#download-box').hide();
                }
                if($('.pending-task:checked').length > 0){
                    $('.pending-task').prop('checked',false);
                    $('#receive-box').hide();
                }
                if($(undo_task_id+':checked').length > 0){
                    $('#undo-box').show();
                    if(flagShift){
                        var firstValue = 0;
                        var lastValue = 0;
                        $(undo_task_id).each(function(index,ele){
                            if($(ele).val() == $(undo_task_id+':checked').first().val()){
                                firstValue = index;
                            }
                            if($(ele).val() == $(current).val()){
                                lastValue = index;
                            }
                        });
                        if(firstValue < lastValue){
                            for(var i = firstValue ; i < lastValue ; i++){
                                $($(undo_task_id)[i]).prop('checked',true);
                            }
                        }else{
                            for(var i = lastValue ; i < firstValue ; i++){
                                $($(undo_task_id)[i]).prop('checked',true);
                            }
                        }
                    }
                }else{
                    $('#undo-box').hide();
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
            if(type != 'check'){
                switch(role){
                    case 'raw':
                        if(type != 'raw'){
                            e.preventDefault();
                            e.stopPropagation();
                            toastr.warning("The User's Role is not '"+type+"'")
                        }
                        break;
                    case 'clean':
                        if(type != 'raw' && type != 'clean'){
                            e.preventDefault();
                            e.stopPropagation();
                            toastr.warning("The User's Role is not correct!")
                        }
                        break;
                    case 'type':
                        if(type != 'type' && type != 'clean'){
                            e.preventDefault();
                            e.stopPropagation();
                            toastr.warning("The User's Role is not correct!")
                        }
                        break;
                    case 'sfx':
                        if(type != 'type' && type != 'sfx'){
                            e.preventDefault();
                            e.stopPropagation();
                            toastr.warning("The User's Role is not correct!")
                        }
                        break;
                    case 'check':
                        if(type != 'sfx'){
                            e.preventDefault();
                            e.stopPropagation();
                            toastr.warning("The User's Role is not correct!")
                        }
                        break;
                    default:
                }
            }else{
                if(check == 0){
                    e.preventDefault();
                    e.stopPropagation();
                    toastr.warning("The User's Role is not correct!")
                }
            }
        })
    }
    $(document).on('click','#task-btn',function(e){
        e.preventDefault();
        // setInterval(checkProcess, 5000);
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

    $(document).on('click','#undo-task-btn',function(e){
        e.preventDefault();
        // setInterval(checkProcess, 5000);
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin"></i>');
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var undo_task_id = '.'+role+'-undo-task:checked';
        var arrayIDTask = [];
        $(undo_task_id).each(function(key, ele){
            arrayIDTask.push($(ele).val());
        })
        
        $('#undo-page-task [name=id_tasks]').val(arrayIDTask.join(','));
        $('#undo-page-task [name=type_task]').val(role);

        $('#undo-page-task').trigger('submit');
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
            var fileName = (new URL(url)).searchParams.get('fileName');
            // $('#modal-show-images').find('img').attr('src',url);
            $('#title-show-image').text('');
            $('#title-show-image').text(type+ ': '+fileName);
            if(type === 'sfx' && role === 'check'){
                // $('#modal-show-images').find('img').data('hasAction','1');
                $('#action-check').show();
            }else{
                // $('#modal-show-images').find('img').data('hasAction','0');
                $('#action-check').hide();
            }
            $('#modal-show-images').modal('show');
            fetchData({},url,'GET',loading(true)).done(function(data){
                $('.image-page.prev').attr('src',data.src.prev);
                $('.image-page.current').attr('src',data.src.current);
                $('.image-page.next').attr('src',data.src.next);
                 // loading(true);
            });
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
        if(flagShow && !$('#modal-note-page').hasClass('show')){
        flagShow=false;
        var url = $(this).data('url');
        var prev = $('.image-page.prev');
        var current =  $('.image-page.current');
        var next =  $('.image-page.next');
        if(prev.attr('src') !== ''){
        var fileName = (new URL(prev.attr('src'))).searchParams.get('fileName');
        var type = (new URL(prev.attr('src'))).searchParams.get('type');
        $('#title-show-image').text('');
        $('#title-show-image').text(type+ ': '+fileName);
        fetchData({type:type,fileName:fileName,volume_id:volume_id_page},url,'GET',loading(true)).done(function(data){
            next.addClass('prev').removeClass('next');
            current.addClass('next').removeClass('current');
            prev.addClass('current').removeClass('prev');
            $('.image-page.prev').attr('src',data.src.prev);
            // flagShow=true;
            loadingCheck(false);
            if(document.querySelector('.image-page.current').complete){
                loading(false);
            }
        }).fail(function(){
            toastr.error("There were errors. Please try again.");
        });
        }else{
            toastr.options = {
                "positionClass": "toast-top-center toast-center",
                "preventDuplicates": true,
            }
            toastr.warning("Not Found!");
            toastr.options = {
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
            }
            // flagShow=true;
            loadingCheck(false);
            loading(false);
        }
        }
    });

    $('.image-arrow.right').on('click',function(e){
        if(flagShow && !$('#modal-note-page').hasClass('show')){
        flagShow = false
        var url = $(this).data('url');
        var prev = $('.image-page.prev');
        var current =  $('.image-page.current');
        var next =  $('.image-page.next');
        if(next.attr('src') !== ''){
            var fileName = (new URL(next.attr('src'))).searchParams.get('fileName');
            var type = (new URL(next.attr('src'))).searchParams.get('type');
            $('#title-show-image').text('');
            $('#title-show-image').text(type+ ': '+fileName);
            fetchData({type:type,fileName:fileName,volume_id:volume_id_page},url,'GET',loading(true)).done(function(data){
                prev.addClass('next').removeClass('prev');
                current.addClass('prev').removeClass('current');
                next.addClass('current').removeClass('next');
                $('.image-page.next').attr('src',data.src.next);
                // flagShow=true;
                loadingCheck(false);
                if(document.querySelector('.image-page.current').complete){
                    loading(false);
                }
            }).fail(function(){
                toastr.error("There were errors. Please try again.");
            });
        }else{
            toastr.options = {
                "positionClass": "toast-top-center toast-center",
                "preventDuplicates": true,
            }
            toastr.warning("Not Found!");
            toastr.options = {
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
            }
            // flagShow=true;
            loadingCheck(false);
            loading(false);
        }
        }
    });

    var loading = function(status){
        if(status){
            flagShow = false;
            $('#skeleton').show();
            $('.image-page.current').hide();
            // setTimeout(load,000);
        }else{
            flagShow = true;
            $('#skeleton').hide();
            $('.image-page.current').show();
        }
    }

    $('.image-page.current').on('load',function(){
        // var role = sessionStorage.getItem('authRole').toLowerCase();
        // var fileName = (new URL($(this).attr('src'))).searchParams.get('fileName');
        // var textHead = $('#title-show-image').text().split(':');
        // $('#title-show-image').text('');
        // $('#title-show-image').text(textHead[0]+': '+fileName);
        // if(($(this).data('hasAction') == 1)&& role === 'check'){
        //     $('#action-check').show();
        // }else{
        //     $('#action-check').hide();
        // }
        loadingCheck(false);
        loading(false);
    })

    $('.reject-check').on('click',function(e){
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var fileName = (new URL($('#modal-show-images').find('.image-page.current:visible').attr('src'))).searchParams.get('fileName');
        var type = (new URL($('#modal-show-images').find('.image-page.current:visible').attr('src'))).searchParams.get('type');
        if(type === 'SFX' && role === 'check'){
            $('#reject-check-form').find('[name=note]').val('');
            $('#reject-check-form').find('[name=fileName]').val(fileName);
            $('#reject-check-form').find('#note_image_preview').removeAttr('style');
            $('#reject-check-form').find('.container-nip').removeAttr('style');
            blob = '';
            $('#reject-check-form').find('#name_note_image').text('');
            $('#reject-check-form').find('#note_image_page').val('');
            
            $('#modal-note-page').modal('show');
        }else{
            toastr.error("Roles user or file is not correct!");
        }
    })

    $('.done-check').on('click',function(e){
        var role = sessionStorage.getItem('authRole').toLowerCase();
        var fileName = (new URL($('#modal-show-images').find('.image-page.current:visible').attr('src'))).searchParams.get('fileName');
        var type = (new URL($('#modal-show-images').find('.image-page.current:visible').attr('src'))).searchParams.get('type');
        if(type === 'SFX' && role === 'check'){
            fetchData({fileName:fileName,volume_id:volume_id_page},url_done_check,'GET',loadingCheck(true)).done(function(data){
                if(data.code == 200){
                    $('.image-arrow.right:visible').trigger('click');
                }
                if(data.code == 404){
                    toastr.error("Not permission!");
                    loadingCheck(false);
                    loading(false);
                }
                if(data.code == 500){
                    toastr.error("There were errors. Please try again.");
                    loadingCheck(false);
                    loading(false);
                }
            }).fail(function(){
                toastr.error("There were errors. Please try again.");
            });
        }else{
            toastr.error("Roles user or file is not correct!");
        }
    })

    $('#reject-check-form').on('submit',function(e){
        e.preventDefault();
        var url_reject_check = $(this).attr('action');
        var allowImageType = ['image/jpeg','image/png'];
        if(document.getElementById('note_image_page').files.length > 0){
            var typeFile = document.getElementById('note_image_page').files[0].type
            if($.inArray(typeFile,allowImageType) === -1){
                toastr.error("Must be a file of Image");
                return false;
            }
        }
        var data = new FormData(this);
        if(blob){
            data.append('note_image', blob);
            blob ='';
        }
        $('#modal-note-page').modal('hide');
        $.ajax({
            url: url_reject_check,
            method: "POST",
            data: data,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend:loadingCheck(true),
        }).done(function(data){
            if(data.code == 200){
                $('.image-arrow.right:visible').trigger('click');
            }
            if(data.code == 404){
                toastr.error("Not permission!");
                loadingCheck(false);
                loading(false);
            }
            if(data.code == 500){
                toastr.error("There were errors. Please try again.");
                loadingCheck(false);
                loading(false);
            }
            }).fail(function(){
                toastr.error("There were errors. Please try again.");
            });
    })

    var loadingCheck = function(status){
        if(status){
            flagReload = true;
            $('#modal-show-images').find('.close-check').prop('disabled',true);
            $('#modal-show-images').find('.reject-check').prop('disabled',true);
            $('#modal-show-images').find('.done-check').prop('disabled',true);
            $('#skeleton').show();
            $('.image-page.current').hide();
        }else{
            $('#modal-show-images').find('.close-check').prop('disabled',false);
            $('#modal-show-images').find('.reject-check').prop('disabled',false);
            $('#modal-show-images').find('.done-check').prop('disabled',false);
        }
    }

    $('.zoom-in').on('click',function(){
        var lv_zoom = $('#modal-show-images').find('img.image-page').data('lvzoom') || '0';
        switch(lv_zoom){
            case '0':
            $('#modal-show-images').find('img.image-page').data('lvzoom','1');
            $('#modal-show-images').find('img.image-page').css('width','150%');
            break;
            case '1':
            $('#modal-show-images').find('img.image-page').data('lvzoom','2');
            $('#modal-show-images').find('img.image-page').css('width','200%');
            break;
            default:
            $('#modal-show-images').find('img.image-page').data('lvzoom','2');
            $('#modal-show-images').find('img.image-page').css('width','200%');
        }
        
    })

    $('.zoom-out').on('click',function(){
        var lv_zoom = $('#modal-show-images').find('img.image-page').data('lvzoom') || '0';
        switch(lv_zoom){
            case '1':
            $('#modal-show-images').find('img.image-page').data('lvzoom','0');
            $('#modal-show-images').find('img.image-page').css('width','100%');
            break;
            case '2':
            $('#modal-show-images').find('img.image-page').data('lvzoom','1');
            $('#modal-show-images').find('img.image-page').css('width','150%');
            break;
            default:
            $('#modal-show-images').find('img.image-page').data('lvzoom','0');
            $('#modal-show-images').find('img.image-page').css('width','100%');
        }
        
    })

    $('#note_image_page').on('change',function(e){
        blob = '';
        previewRejectCheck(e.target,$('#note_image_preview'));
    });

    function previewRejectCheck(element,imagePreview) {
        let img = element.files[0];
        // $('#name_note_image').text(': '+img.name);
        let reader = new FileReader();
        reader.onloadend = function() {
            imagePreview.css("background-image", "url('"+reader.result+"')");
        };
        imagePreview.closest('.container-nip').css('display','block');
        reader.readAsDataURL(img);
    };

    $(document).mouseup(function(e) 
    {
        var container = $(".popover");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            if(!(($('[data-toggle="popover"]').is(e.target) || $('[data-toggle="popover"]').has(e.target).length > 0) && $(e.target).closest('[data-toggle=popover]').attr('aria-describedby') == container.attr('id'))){
                $('[data-toggle="popover"]').popover('hide');
            }
        }
    });

    $("html").on("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
   
      $("html").on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
   
      $('#dropZ').on('dragover', function () {
        $(this).addClass('drag_over');
        return false;
      });
   
      $('#dropZ').on('dragleave', function () {
        $(this).removeClass('drag_over');
        return false;
      });

      $('#dropZ').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('drag_over');
        blob = '';
        var files = e.originalEvent.dataTransfer.files;
        document.getElementById('note_image_page').files = files;
        $('#note_image_page').trigger('change');
    });
    // function checkProcess(){
    //     fetchData({},url_check_process,'GET').done(function(data){
    //         console.log(data)
    //     })
    // }
    document.onpaste = function(pasteEvent) {
        var item = pasteEvent.clipboardData.items[0];
        if (item.type.indexOf("image") === 0)
        {
            blob = item.getAsFile();
            document.getElementById('note_image_page').value = '';
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#note_image_preview').css("background-image", "url('"+event.target.result+"')");
            };
            $('#note_image_preview').closest('.container-nip').css('display','block');
            reader.readAsDataURL(blob);
        }
    }
});