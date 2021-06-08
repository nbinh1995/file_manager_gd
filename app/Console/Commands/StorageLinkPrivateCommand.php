<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageLinkPrivateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:private';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link "root" to "public/storage"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists(config('filesystems.disks.private.visibility').'/storage')) {
            return $this->error('The "public/storage" directory already exists.');
        }

        if (!file_exists(config('filesystems.disks.private.root'))) {
            return $this->error('The "root" directory does not already exist.');
        }

        $this->laravel->make('files')->link(
            config('filesystems.disks.private.root'), config('filesystems.disks.private.visibility').'/storage'
        );

        $this->info('The [public/storage] directory has been linked.');
    }
}
