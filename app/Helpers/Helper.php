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
                $hasDownFile = $page->sfx === 'done' ? true : false;
                $user = $page->checkUser->username ?? '';
            break;
            default:
            $status = $page->raw;
            $hasDownFile = false;
            $user = $page->rawUser->username ?? '';
        }
        switch($status){
            case 'doing':
                $badge = '<label class="btn btn-warning btn-xs text-monospace">Doing: '.$user.'</label>';
            break;
            case 'done': 
                $badge = '<label class="btn btn-success btn-xs  text-monospace '.$type.'-detail" data-url="'.route('file-manager.showImage',['type'=>config('lfm.vol')[$type],'page_id'=>$page->id]).'"><i class="fas fa-images"></i> Done: '.$user.' </label>';
            break;
            default:
            $badge = $hasDownFile ? '<label class="btn btn-danger btn-xs text-monospace"><input type="checkbox" value="'.$page->id.'" class="task-checkbox align-text-bottom '.$type.'-task-id"> Pending</label>' : '<label class="btn btn-danger btn-xs text-monospace">Pending</label>' ;
        }
        
        return $badge;
    }
}

?>
