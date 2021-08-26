$(document).ready(function(event){
    var fetchData = function (data, ajaxUrl, method = 'post', beforeSend = null) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        return $.ajax({
            data: data,
            dataType: 'json',
            url: ajaxUrl,
            method: method,
            beforeSend: beforeSend,
        });
    };
    $(document).on('change','#book-home',function(e){
        
        fetchData({book: $(e.target).val()},url_get_vol_by_book).done(function(data){
            var html = '<option selected disabled>----/----</option>';
            $.each(data.volumes,function(index,item){
                var rate_sfx = item.pages_sfx_done_count+'/'+item.pages_count;
                var rate_check = item.pages_check_done_count+'/'+item.pages_count
                html += '<option value="'+item.id+'">'+item.filename+'&nbsp;&nbsp;&nbsp;&nbsp;<b>(SFX)</b>: '+rate_sfx+'&nbsp;&nbsp;&nbsp;&nbsp;<b>(Check)</b>: '+rate_check+'</option>'
            })
            $('#volume-home').html('');
            $('#volume-home').html(html);
        }).fail(function(){
            toastr.error("There were errors. Please try again.")
        })
    })
});