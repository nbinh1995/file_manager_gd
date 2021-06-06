<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;

class HasUploadedImageListener
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
    public function handle(ImageWasUploaded $event)
    {
        if(strpos($event->path(), '.psd') !== false){
            $publicFilePath = str_replace(storage_path('app/public'), "", $event->path());
            $publicFileImg = str_replace('.psd', ".png", $publicFilePath);
            \Image::configure(array('driver' => 'imagick'));
            $file = \Image::make(public_path('/storage'.$publicFilePath))->encode('png');
            $file->save(public_path('/storage'.$publicFileImg));
        }
    }
}
