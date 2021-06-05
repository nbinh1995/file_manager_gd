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
        switch($type){
            case 'clean':
                $status = $page->clean;
                $user = $page->cleanUser->username ?? '';
            break;
            case 'type': 
                $status = $page->type;
                $user = $page->typeUser->username ?? '';
            break;
            case 'sfx': 
                $status = $page->sfx;
                $user = $page->sfxUser->username ?? '';
            break;
            case 'check': 
                $status = $page->check;
                $user = $page->checkUser->username ?? '';
            break;
            default:
            $status = $page->raw;
            $user = $page->rawUser->username ?? '';
        }
        switch($status){
            case 'doing':
                $badge = '<span class="badge badge-warning">'.$user.'</span>';
            break;
            case 'done': 
                $badge = '<span class="badge badge-success">'.$user.'</span>';
            break;
            default:
            $badge = '<span class="badge badge-danger">Pending</span>';
        }
        return $badge;
    }
}

?>
