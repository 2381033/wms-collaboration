<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductExpiredReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productExpired:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about product expired.';

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
        // group by user

        // send email

        // $this->sendEmail($job);

        return 0;
    }
}
