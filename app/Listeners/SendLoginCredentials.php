<?php

namespace App\Listeners;

use App\Events\RegistrationSuccess;
use App\Mail\MyMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoginCredentials
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  RegistrationSuccess  $event
     * @return void
     */
    public function handle(RegistrationSuccess $event)
    {
        try{
            $emaildata = new \stdClass();
            $emaildata->subject = "Registration Success!! ";
            $emaildata->message="Your Login credentials are \n Email ".$event->usercredentials->email."\n Password ".$event->usercredentials->password ;
        Mail::to($event->usercredentials->email)->send(new MyMail($emaildata));
        }
        catch (\Exception $ex)
        {
            Log::error('An error occured when sending Registration Mail '.$ex);
        }
    }
}
