<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-old-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old backups older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all files in the backups directory on S3
        $files = Storage::disk('s3')->files('backups');

        $deletedFilesCount = 0;

        foreach ($files as $file) {
            // Get the last modified time of the file
            $lastModified = Carbon::createFromTimestamp(Storage::disk('s3')->lastModified($file));

            // Check if the file is older than 30 days
            if ($lastModified->lt(Carbon::now()->subDays(30))) {
                // Delete the file
                Storage::disk('s3')->delete($file);
                $deletedFilesCount++;
            }
        }

        $this->info("Cleanup completed. Deleted $deletedFilesCount old backup(s).");
    }
}
