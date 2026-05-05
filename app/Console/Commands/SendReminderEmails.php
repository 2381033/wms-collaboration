<?php

namespace App\Console\Commands;

use App\Mail\RegisterUserEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\Models\Transaction\Inbound\Job as InboundJob;

class SendReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about reminders.';

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
        // get all reminders for today
        $job = InboundJob::first();
        // group by user

        // send email

        $this->sendEmail($job);

        return 0;
    }

    private function sendEmail($data) {
        $user = \App\User::find(1);

        Mail::to($user)->send(new RegisterUserEmail($data));
    }
}
