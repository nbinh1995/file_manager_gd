$(document).ready(function(event){
    $('button[type=submit]').on('click', function(event){
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin mr-2"></i><span>Saving...</span>');
        $('form').submit();
    });

    $('input[name="role_multi[]"]').on('click',function(e){
        var role_default =  $('#role').val();
        $('#role').html('');
        $('input[name="role_multi[]"]:checked').each(function(index,ele){
            if(role_default && $(ele).val() == role_default){
                $('#role').append('<option value="'+$(ele).val()+'" selected >'+$(ele).val()+'</option>');
            }else{
                $('#role').append('<option value="'+$(ele).val()+'">'+$(ele).val()+'</option>');
            }
        })
    });
});