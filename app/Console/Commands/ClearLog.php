<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customlog:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom command to clear log';

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
     * @return int
     */
    public function handle()
    {
        // file_put_contents(storage_path('logs/haltermqtt.log'), '');
        file_put_contents(storage_path('logs/laravel.log'), '');

        $this->info('Log Cleared');

        return 0;
    }
}
