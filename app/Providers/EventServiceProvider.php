<?php

namespace App\Providers;

use App\Listeners\DeleteImageListener;
use App\Listeners\HasUploadedImageListener;
use App\Listeners\IsUploadingImageListener;
use App\Listeners\RenameFolderListener;
use App\Listeners\RenameImageListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageIsDeleting;
use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageIsUploading;
use UniSharp\LaravelFilemanager\Events\ImageWasUploaded;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        ImageIsDeleting::class => [
            DeleteImageListener::class
        ],
        ImageIsRenaming::class => [
            RenameImageListener::class
        ],
        ImageIsUploading::class => [
            IsUploadingImageListener::class
        ],
        ImageWasUploaded::class => [
            HasUploadedImageListener::class
        ],
        FolderIsRenaming::class => [
            RenameFolderListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
