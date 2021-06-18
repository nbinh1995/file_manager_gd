<?php

if (!function_exists('getDirGoogleDrive')) {
function getDirGoogleDrive($dir){
    $contents = $dir->where('type','dir')->map(function($item){
        $item['level'] = count(explode('/',$item['path']));
        return $item;
    });
    $levelMax = $contents->max(('level'));
    while ($levelMax > 0) {
        $contents = $contents->map(function($item) use ($contents){
            $item['child'] = $contents->where('dirname',$item['path'])->all();
            return $item;
        });
        $levelMax--;
    }
    return $contents->where('dirname','');
}}

if (!function_exists('showHtmlDir')) {
    function showHtmlDir($dir,$html = ''){
        $html = '';
        foreach($dir as $item){
            $html .='<option value="'.$item['path'].'">'.str_repeat("&#9552;", (int)$item['level']).'&#9569;'.$item['filename'].'</option>';
            if(count($item['child']) > 0){
                $html.= showHtmlDir($item['child'],$html);
            }
        }
        return $html;
    }
}
if (!function_exists('showRawStatus')) {
    function showPageStatus($page,$type){
        $status = '';
        $user = '';
        $badge = '';
        $hasDownFile = false;
        switch($type){
            case 'clean':
                $status = $page->clean;
                $hasDownFile = $page->raw === 'done' ? true : false;
                $user = $page->cleanUser->username ?? '';
            break;
            case 'type': 
                $status = $page->type;
                $hasDownFile = $page->clean === 'done' ? true : false;
                $user = $page->typeUser->username ?? '';
            break;
            case 'sfx': 
                $status = $page->sfx;
                $hasDownFile = $page->type === 'done' ? true : false;
                $user = $page->sfxUser->username ?? '';
            break;
            case 'check': 
                $status = $page->check;
                $hasDownFile = false;
                $user = $page->checkUser->username ?? '';
            break;
            default:
            $status = $page->raw;
            $hasDownFile = false;
            $user = $page->rawUser->username ?? '';
        }
    
        switch($status){
            case 'doing':
                $badge = $type === 'check' ? '<label class="btn btn-warning btn-xs text-monospace">Reject: '.$user.'</label> <label class="btn btn-xs btn-outline-danger border-0" data-toggle="popover" title="Reject Note" data-content="'.($page->note ?? '...').'"><i class="fas fa-question-circle"></i></label>' :'<label class="btn btn-warning btn-xs text-monospace"><input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom doing-task '.$type.'-undo-task"> Doing: '.$user.'</label>';
            break;
            case 'done': 
                $badge = '<label class="btn btn-success btn-xs  text-monospace '.$type.'-detail" data-url="'.route('file-manager.showImage',['volume_id'=>$page->volume_id,'type'=>config('lfm.vol')[$type],'fileName'=>$page->filename]).'"><i class="fas fa-images"></i> Done: '.$user.' </label>';
            break;
            default:
            $badge = $hasDownFile ? '<label class="btn btn-danger btn-xs text-monospace"><input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom pending-task '.$type.'-task-id"> Pending</label>' : '<label class="btn btn-danger btn-xs text-monospace">Pending</label>' ;
        }
        
        return $badge;
    }
}

if (!function_exists('convert_name')) {
function convert_name($str) {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    // $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
    $str = preg_replace("/( )/", '_', $str);
    return $str;
}
}
?>
