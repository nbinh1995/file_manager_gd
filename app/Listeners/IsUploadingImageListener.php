<?php

namespace App\Listeners;

use App\Models\Page;
use App\Models\Volume;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
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
        if(strpos($event->path(), 'Old') === false){
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
            $publicFilePath = str_replace(config('filesystems.disks.private.root').'/', "", $event->path());
            preg_match($regex, $publicFilePath,$pathVolume);
            $volume = Volume::where('path',$pathVolume[1])->first();
            if($volume instanceof Volume){
                $basename = str_replace($pathVolume[0].'/', "", $publicFilePath);
                $filename = explode('.',$basename)[0];
                $page =null;
                $page = Page::where('filename',$filename)->where('volume_id',$volume->id)->first();
                if(!$page instanceof Page && $type !== 'raw'){
                        throw new \Exception('The file name "'.$filename.'" does not exist in "Raw" directory!');
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
                            if(Storage::disk(config('lfm.disk'))->exists($publicFilePath)){
                                $lastModified = date('Ymd_His',Storage::disk(config('lfm.disk'))->lastModified($publicFilePath));
                                $newPublicFilePath = explode('.',$publicFilePath);
                                $newPublicFilePath[0] = $newPublicFilePath[0].'_'.$lastModified;
                                $newPublicFilePath = implode('.',$newPublicFilePath);
                                $page->update([
                                    'check' => 'pending',
                                    'check_id' => null,
                                    'note' => null,
                                ]);
                                Storage::disk(config('lfm.disk'))->move($publicFilePath,$newPublicFilePath);
                            }else{
                                $page->update([
                                    'sfx' => 'done',
                                    'sfx_id' => auth()->id()
                                ]);
                            }
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
                    if(preg_match('/^[\d]{1,3}$/', $filename) == 0){
                        throw new \Exception('The file name is numeric');
                    }
                    if(!($page instanceof Page) && $type === 'raw'){
                        Page::create([
                            'filename' => $filename,
                            'volume_id' => $volume->id,
                            'raw_id' => auth()->id(),
                        ]);
                    }
                    // else{ 
                    //     throw new \Exception('The file name "'.$filename.'" is exist in "Raw" directory!');
                    // }
                }
            }
        }
        }
    }
}
