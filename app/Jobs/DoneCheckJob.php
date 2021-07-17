<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class DoneCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $page;
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
        $folderPath = $this->page->volume->path.'/'.config('lfm.vol.sfx');
        $newFilePath = $this->page->volume->path.'/'.config('lfm.vol.check').'/'.$this->page->filename;
        $filesDone = collect(Storage::disk(config('lfm.disk'))->listContents($folderPath,false))->where('filename',$this->page->filename)->first();
        $publicFilePath = '/'.$filesDone['path'];
        if($filesDone['extension'] === 'psd'){
            if(file_exists(config('filesystems.disks.private.root').'/'.$newFilePath.'.png')){
                unlink(config('filesystems.disks.private.root').'/'.$newFilePath.'.png');
            }
            \Image::configure(array('driver' => 'imagick'));
            $file = \Image::make(config('filesystems.disks.private.root').$publicFilePath)->encode('png');
            $file->save(config('filesystems.disks.private.root').'/'.$newFilePath.'.png');
        }else{
            if(file_exists(config('filesystems.disks.private.root').'/'.$newFilePath.'.'.$filesDone['extension'])){
                unlink(config('filesystems.disks.private.root').'/'.$newFilePath.'.'.$filesDone['extension']);
            }
            Storage::disk(config('lfm.disk'))->copy($publicFilePath,$newFilePath.'.'.$filesDone['extension']);
        }
        }catch(\Exception $e){
            throw new Exception('Server Error!',500);
        }
    }
}
