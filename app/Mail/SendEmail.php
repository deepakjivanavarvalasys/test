<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $email_template;
    /**
     * @var array
     */
    private $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_template, $subject, $details =array())
    {
        //
        $this->email_template = $email_template;
        $this->details = $details;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!empty($this->subject)) {
            $subject = $this->subject;
        } else {
            $subject = 'Valasys Media Program Management';
        }
        return $this->subject($subject)->view($this->email_template, $this->details);
    }
}
