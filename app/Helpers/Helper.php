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
            $html .='<option value="'.$item['path'].'">'.$item['level'].str_repeat("-", (int)$item['level']).$item['filename'].'</option>';
            if(count($item['child']) > 0){
                $html.= showHtmlDir($item['child'],$html);
            }
        }
        return $html;
    }
}
?>
