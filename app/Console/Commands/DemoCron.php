<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\DemoMail;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mailData = [
            'title' => 'Mail from Shivam',
            'body' => 'This is for testing email using smtp.'
        ];
         
        Mail::to('smkatiyar97@gmail.com')->send(new DemoMail($mailData));
           
        //dd("Email is sent successfully.");
    }
}
