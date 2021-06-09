<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanBackupFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cleanByDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean automatically backup file after day(s) form settings.';

    protected $days;

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
        try {
            $path = preg_replace('/[^a-zA-Z0-9.]/', '-', env('APP_URL'));
            $backupFiles = Storage::disk('local')->files($path);
            if (empty($backupFiles)) {
                throw new \Exception('Can not get the backup folder.');
            }
            $count = 0;
            foreach ($backupFiles as $backupFile) {
                if (!Str::contains(basename($backupFile), 'man-')) {
                    $fullPath = storage_path('app') . '/' . $backupFile;

                    if (abs(Carbon::createFromTimestamp(filemtime($fullPath))->diffInDays(Carbon::now())) > Setting::get('backup_days', config('job.keep-days'))) {
                        unlink($fullPath);
                        $count++;
                    }
                }
            }
            Log::info(sprintf('Successful clean %d Backup file(s) at %s', $count, Carbon::now()->format('Y-m-d H:i:s')));
        } catch (\Exception $exception) {
            Log::error(sprintf('Clean Backup file failed: %s', $exception->getMessage()));
        }
    }
}
