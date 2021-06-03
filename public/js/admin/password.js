$(document).ready(function(event){
    $('button[type=submit]').on('click', function(event){
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin mr-2"></i><span>Saving...</span>');
        $('form').submit();
    });
});