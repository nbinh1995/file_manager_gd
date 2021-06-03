const fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
    return $.ajax({
        data: data,
        dataType: 'json',
        url: ajaxUrl,
        method: method,
        beforeSend: beforeSend,
    });
};

const ajaxGetFolder = function(path,ele){
    fetchData({path:path},'/ajax/ajaxGetFolder','get').done(function(data){
        if(data.dir.length){
            html ='  <div class="form-group box-dir">    <select name="dir"  class="form-control dir"> <option disabled selected>----/----</option>';
            $.each( data.dir,function(index,value){
                html += '<option value="'+value.path+'">'+value.filename+'</option>'
            });
            html +=' </select>   </div>';
            console.log(html);
            $(ele).closest('.form-group').find('~ .box-dir').remove();
            $(ele).closest('.form-group').after(html);
        }
    })
}

$(document).on('change','.dir',function(e){
    ajaxGetFolder($(this).val(),this);
});