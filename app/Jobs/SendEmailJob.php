<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email_template;
    private $to;
    private $subject;
    private $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_template, $to, $subject, $details = array())
    {
        $this->email_template = $email_template;
        $this->to = $to;
        $this->subject = $subject;
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sendEmail = new SendEmail($this->email_template, $this->subject, $this->details);
        Mail::to($this->to)->send($sendEmail);
    }



    /**
     * Sample code to send email using this job
     *
     * dispatch(new SendEmailJob('email.test', $to, 'This is test subject', $details));     *
     *
     */
}
