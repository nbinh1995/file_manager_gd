<?php

namespace App\Listeners;

use App\Models\Page;
use App\Models\Volume;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use phpDocumentor\Reflection\Types\Void_;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;

class IsUploadingImageListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ImageIsUploading $event)
    {   
        if(!str_contains($event->path(), 'Old')){
                $regex = null;
                $type = 'raw';
        foreach(config('lfm.volume') as $key => $type){
            if(strpos($event->path(), $type) !== false){
                $regex = '/^(.*?)\/'.$type.'/';
                $type = $key;
                break;
            }
        }
       
        if($regex){
            $publicFilePath = str_replace(storage_path('app/public').'/', "", $event->path());
            preg_match($regex, $publicFilePath,$pathVolume);
            $volume = Volume::where('path',$pathVolume[1])->first();
            if($volume instanceof Volume){
                $filename = str_replace($pathVolume[0].'/', "", $publicFilePath);
                $filename = explode('.',$filename)[0];
                $page =null;
                if($type !== 'raw'){
                    $page = Page::where('filename',$filename)->where('volume_id',$volume->id)->first();
                    if(!$page instanceof Page){
                        throw new \Exception('The file name "'.$filename.'" does not exist in "Raw" directory!');
                    }
                }
                switch($type){
                    case 'clean':
                        if($page instanceof Page && $page->raw === 'done'){
                            $page->update([
                                'clean' => 'done',
                                'clean_id' => auth()->id()
                            ]);
                        }else{
                            throw new \Exception('The file name "'.$filename.'" does not exist in "Raw" directory!');
                        }
                    break;
                    case 'type':
                        if($page instanceof Page && $page->clean === 'done'){
                            $page->update([
                                'type' => 'done',
                                'type_id' => auth()->id()
                            ]);
                        }else{
                            throw new \Exception('The file name "'.$filename.'" does not exist in "Clean" directory!');
                        }
                    break;
                    case 'sfx':
                        if($page instanceof Page && $page->type === 'done'){
                            $page->update([
                                'sfx' => 'done',
                                'sfx_id' => auth()->id()
                            ]);
                        }else{
                            throw new \Exception('The file name "'.$filename.'" does not exist in "SFX" directory!');
                        }
                    break;
                    case 'check':
                        if($page instanceof Page && $page->sfx === 'done'){
                            $page->update([
                                'check' => 'done',
                                'check_id' => auth()->id()
                            ]);
                        }else{
                            throw new \Exception('The file name "'.$filename.'" does not exist in "Check" directory!');
                        }
                    break;
                    default:
                    Page::create([
                        'filename' => $filename,
                        'volume_id' => $volume->id,
                        'raw_id' => auth()->id(),
                    ]);
                }
            }
        }
        }
    }
}
