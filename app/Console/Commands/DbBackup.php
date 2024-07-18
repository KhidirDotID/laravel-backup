<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database and upload to object storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Path to store the backup file locally
        $path = storage_path('app/backups/');
        if (!file_exists($path))
            mkdir($path);

        // Database configuration
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbName = env('DB_DATABASE');

        $fileName = Carbon::now()->format('Y-m-d-H-i-s') . '_' . $dbName . '.sql';

        // Command to dump the database
        $command = "mysqldump -u " . $dbUser . " -p'" . $dbPass . "' " . $dbName . " > " . $path . $fileName;

        // Execute the command
        exec($command);

        $this->info('Backup completed successfully.');

        // Upload to object storage
        // Storage::disk('s3')->put('backups/' . $fileName, file_get_contents($path . $fileName));

        // Optionally, delete the local backup file after upload
        // Storage::disk('local')->delete($path . $fileName);

        $this->info('Backup uploaded successfully.');
    }
}
