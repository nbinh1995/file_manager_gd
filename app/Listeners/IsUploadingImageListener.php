<?php

namespace App\Listeners;

use App\Models\HistoryUF;
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
        if(strpos($event->path(), 'Reference') === false){
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
                        if(strpos(auth()->user()->role_multi,'Clean') !== false || auth()->id() == 1){
                            if($page instanceof Page && $page->raw === 'done'){
                                if($page->clean_image){
                                    if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->clean_image)){
                                        unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->clean_image);
                                    }
                                }
                                $page->update([
                                    'clean' => 'done',
                                    'clean_id' => auth()->id(),
                                    'clean_image'=>null
                                ]);
                            }else{
                                throw new \Exception('The file name "'.$filename.'" does not exist in "Raw" directory!');
                            }
                        }else{
                            throw new \Exception('Not permission!');
                        }
                    break;
                    case 'type':
                        if(strpos(auth()->user()->role_multi,'Type') !== false || auth()->id() == 1){
                            if($page instanceof Page && $page->clean === 'done'){
                                if($page->type_image){
                                    if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->type_image)){
                                        unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->type_image);
                                    }
                                }
                                $page->update([
                                    'type' => 'done',
                                    'type_id' => auth()->id(),
                                    'type_image' => null
                                ]);
                            }else{
                                throw new \Exception('The file name "'.$filename.'" does not exist in "Clean" directory!');
                            }
                        }else{
                            throw new \Exception('Not permission!');
                        }
                    break;
                    case 'sfx':
                        if(strpos(auth()->user()->role_multi,'SFX') !== false || strpos(auth()->user()->role_multi,'Check') !== false || auth()->id() == 1){
                            if($page instanceof Page && $page->type === 'done'){
                                if($page->sfx_image){
                                    if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->sfx_image)){
                                        unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->sfx_image);
                                    }
                                }
                                $checkPublicFilePath = str_replace('SFX', "Check", $pathVolume[0]);
                                if(Storage::disk(config('lfm.disk'))->exists($publicFilePath) && $page->sfx !== 'pending'){
                                    $lastModified = date('Ymd_His',Storage::disk(config('lfm.disk'))->lastModified($publicFilePath));
                                    $newPublicFilePath = explode('.',$publicFilePath);
                                    $newPublicFilePath[0] = $newPublicFilePath[0].'_'.$lastModified;
                                    $newPublicFilePath = implode('.',$newPublicFilePath);
                                    if($page->check === 'done'){
                                        if($page->note_image && file_exists(config('filesystems.disks.private.root').'/'.config('lfm.note_folder').'/'.$page->note_image)){
                                            unlink(config('filesystems.disks.private.root').'/'.config('lfm.note_folder').'/'.$page->note_image);
                                        }
                                        if(file_exists(config('filesystems.disks.private.root').'/'.$checkPublicFilePath.'/'.$filename.'.png')){
                                            unlink(config('filesystems.disks.private.root').'/'.$checkPublicFilePath.'/'.$filename.'.png');
                                        }
                                        $page->update([
                                            'sfx_id' => auth()->id(),
                                            'check' => 'pending',
                                            'check_id' => null,
                                            'note' => null,
                                            'note_image' => null,
                                            'sfx_image' => null
                                        ]);
                                    }else{
                                        $page->update([
                                            'sfx_id' => auth()->id(),
                                            'check' => 'pending',
                                            'check_id' => null,
                                            'sfx_image' => null
                                        ]);
                                    }
                                    Storage::disk(config('lfm.disk'))->move($publicFilePath,$newPublicFilePath);
                                }else{
                                    $page->update([
                                        'sfx' => 'done',
                                        'sfx_id' => auth()->id(),
                                        'sfx_image' => null
                                    ]);
                                }
                            }else{
                                throw new \Exception('The file name "'.$filename.'" does not exist in "SFX" directory!');
                            }
                        }else{
                            throw new \Exception('Not permission!');
                        }
                    break;
                    case 'check':
                        if(auth()->user()->is_admin){
                            if($page instanceof Page && $page->sfx === 'done'){
                                if($page->check_image){
                                    if(file_exists(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->check_image)){
                                        unlink(config('filesystems.disks.private.root').'/'.config('lfm.preview_folder').'/'.$page->check_image);
                                    }
                                }
                                $page->update([
                                    'check' => 'done',
                                    'check_id' => auth()->id(),
                                    'check_image' => null
                                ]);
                            }else{
                                throw new \Exception('The file name "'.$filename.'" does not exist in "Check" directory!');
                            }
                        }else{
                            throw new \Exception('Not permission!');
                        }
                    break;
                    default:
                    if(strpos(auth()->user()->role_multi,'Raw') !== false || auth()->id() == 1){
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
                        else{ 
                            $page->update([
                                'raw' => 'done',
                                'raw_id' => auth()->id(),
                            ]);
                            // throw new \Exception('The file name "'.$filename.'" is exist in "Raw" directory!');
                        }
                    }else{
                        throw new \Exception('Not permission!');
                    }
                }
            }

            $history = HistoryUF::create([
                'user_id' => auth()->id(),
                'book' => $volume->book->filename,
                'volume' => $volume->filename,
                'page' => $filename,
                'type' => config('lfm.vol')[$type]
            ]);
            if(!$history instanceof HistoryUF){
                throw new \Exception('Server Error!');
            }
        }
        }
        if(strpos($event->path(), 'Reference') !== false){
            if(strpos(auth()->user()->role_multi,'Raw') !== false || auth()->id() == 1){

            }else{
                throw new \Exception('Not permission!');
            }
        }
    }
}
