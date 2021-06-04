$(document).ready(function() {
    let countFile = 0;
    const fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
        return $.ajax({
            data: data,
            dataType: 'json',
            url: ajaxUrl,
            method: method,
            beforeSend: beforeSend,
        });
    };
    $('#select2').select2({
        theme: 'bootstrap4',
        placeholder: "Please select ...",
        allowClear: true
    });
    // $('#refresh-dir').on('click',function(){
    //     $(this).find('i').addClass('fa-spin');
    // });
    // $('#lfm').filemanager('files');
    var route_prefix = "/laravel-filemanager";
    $('#lfm').filemanager('files', {prefix: route_prefix});
    //drag -drop
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
      $('#btn-files').on('click',function(e){
          e.preventDefault();
        $('#files').trigger( "click" );
       $('#files').on('change',function(e) {
            var files = document.getElementById('files').files;
            uploadFormData(files);
        });
      })
     
      $('#dropZ').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('drag_over');
        var files = e.originalEvent.dataTransfer.files;
        uploadFormData(files);
      });
      
      function uploadFormData(file_obj) {
        $('#files').off('change');
        countFile += file_obj.length;
        $('.count-files').text('Number Of Files Uploaded: '+countFile)
        var formData = new FormData();
        for (var i = 0; i < file_obj.length; i++) {
            formData.append('files[]', file_obj[i]);
        }
        formData.append('type_folder',$('input[type=file]').data('type'));
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }});

        const config = {
          onUploadProgress: function(progressEvent) {
            var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            $('#bar div'). css('width',percentCompleted);
          }
        }
        axios.interceptors.request.use(function (config) {
          $('#bar'). show();
          // Do something before request is sent
          return config;
        }, function (error) {
          // Do something with request error
          return Promise.reject(error);
        });
      
        return axios.post('/ajax/ajaxSaveFile', formData, config)
          .then(function(res){
                  $.each(res.data.images,function(index,item){
                  var img = document.createElement('img');
                  $(img).addClass('img-thumbnail border border-secondary');
                  img.style.height = '240px';
                  img.style.width = '150px';
                  img.src = item;
                  $('#uploaded_file').append(img);
                  });
                  $('#bar'). hide();
                  $('#bar div'). css('width',0);
          })
          .catch(function(err){
            console.log(err);
          });
      }
});
