$(document).ready(function(){
    toastr.options = {
        "preventDuplicates": true,
    }
    if (undefined !== window.flashSuccess) {
        toastr.success(window.flashSuccess);
    }
    
    if (undefined !== window.flashDanger) {
        toastr.error(window.flashDanger);
    }
    
    if (undefined !== window.flashInfo) {
        toastr.info(window.flashInfo);
    }
    
    if (undefined !== window.flashWarning) {
        toastr.warning(window.flashWarning);
    }
    if(sessionStorage.getItem('authRole')){
        $('#auth-role').val(sessionStorage.getItem('authRole'));
    }else{
        setRoleUser();
    }

    $('#auth-role').on('change',function(e){
        setRoleUser(); 
        $('.task-checkbox').prop('checked',false);
    })
    function setRoleUser(){
        // Save data to sessionStorage
        if($('#auth-role').length > 0){
            sessionStorage.setItem('authRole',$('#auth-role').val());
        }
    }
})