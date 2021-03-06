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
        $syncData = ($type === 'sfx' || $type === 'type') && auth()->id() === 1;
        $checkHasNoteCheck = ($page->note || $page->note_image) && $type === 'check';
        switch($type){
            case 'clean':
                $status = $page->clean;
                $hasDownFile = $page->raw === 'done' ? true : false;
                $user = $page->cleanUser->username ?? 'Not Found';
            break;
            case 'type': 
                $status = $page->type;
                $hasDownFile = $page->clean === 'done' ? true : false;
                $user = $page->typeUser->username ?? 'Not Found';
            break;
            case 'sfx': 
                $status = $page->sfx;
                $hasDownFile = $page->type === 'done' ? true : false;
                $user = $page->sfxUser->username ?? 'Not Found';
            break;
            case 'check': 
                $status = $page->check;
                $hasDownFile = false;
                $user = $page->checkUser->username ?? 'Not Found';
            break;
            default:
            $status = $page->raw;
            $hasDownFile = false;
            $user = $page->rawUser->username ?? 'Not Found';
        }
        // <input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom doing-task '.$type.'-undo-task">
        switch($status){
            case 'doing':
                $badge = $type === 'check' ? '<label class="btn btn-warning btn-xs text-monospace">Reject: '.$user.'</label> <label class="btn btn-xs btn-outline-danger border-0" data-toggle="popover" title="Reject Note" data-html="true" data-content="'.($page->note_image ? "<img class='note_image_reject' src='".route('file-manager.showNoteImage',['page_id'=>$page->id])."' alt=''> <hr/>" : '').($page->note ? nl2br(e($page->note)) : '...').'"><i class="fas fa-question-circle"></i></label>' :'<label class="btn btn-warning btn-xs text-monospace"><input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom doing-task '.$type.'-undo-task"> Doing: '.$user.'</label>';
            break;
            case 'done': 
                $badge = $syncData ? '<label class="btn btn-success btn-xs  text-monospace '.$type.'-detail" data-url="'.route('file-manager.bufferImage',['volume_id'=>$page->volume_id,'type'=>config('lfm.vol')[$type],'fileName'=>$page->filename]).'"><i class="fas fa-images"></i> Done: '.$user.' </label><label class="btn btn-xs btn-outline-primary border-0 sync-preview" data-sync="'.route('pages.syncPreview',['page_id'=>$page->id,'type'=>$type]).'"><i class="fas fa-sync"></i></label>' : '<label class="btn btn-success btn-xs  text-monospace '.$type.'-detail" data-url="'.route('file-manager.bufferImage',['volume_id'=>$page->volume_id,'type'=>config('lfm.vol')[$type],'fileName'=>$page->filename]).'"><i class="fas fa-images"></i> Done: '.$user.' </label>' ;
            break;
            default:
            $badge = ($hasDownFile ? '<label class="btn btn-danger btn-xs text-monospace"><input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom pending-task '.$type.'-task-id"> Pending</label>' : '<label class="btn btn-danger btn-xs text-monospace">Pending</label>').(($checkHasNoteCheck)  ? ' <label class="btn btn-xs btn-outline-danger border-0" data-toggle="popover" title="Reject Note" data-html="true" data-content="'.($page->note_image ? "<img class='note_image_reject' src='".route('file-manager.showNoteImage',['page_id'=>$page->id])."' alt=''> <hr/>" : '').($page->note ? nl2br(e($page->note)) : '...').'"><i class="fas fa-question-circle"></i></label>': '') ;
        }

        if(auth()->user()->is_admin){
            $badge ='<input class="reset-'.$type.'-task reset-task" type="checkbox" value="'.$page->id.'" /> '.$badge;
        }
        return $badge;
    }
}

if (!function_exists('convert_name')) {
function convert_name($str) {
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'a', $str);
    $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'e', $str);
    $str = preg_replace("/(??|??|???|???|??)/", 'i', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'o', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'u', $str);
    $str = preg_replace("/(???|??|???|???|???)/", 'y', $str);
    $str = preg_replace("/(??)/", 'd', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'A', $str);
    $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'E', $str);
    $str = preg_replace("/(??|??|???|???|??)/", 'I', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'O', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'U', $str);
    $str = preg_replace("/(???|??|???|???|???)/", 'Y', $str);
    $str = preg_replace("/(??)/", 'D', $str);
    // $str = preg_replace("/(\???|\???|\???|\???|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
    $str = preg_replace("/( )/", '_', $str);
    return $str;
}
}
?>
