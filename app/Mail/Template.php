<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Template extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $template = "template";
    private $datas = [];
    public function __construct($from_name, $from_email, $to_name, $to_email, $subject, $title, $message, $button_link = null, $button_message = null, $file = null)
    {
        $this->from($from_email, $from_name);
        $this->to($to_email, $to_name);

        $this->subject($subject);
        $this->data = [
            'title' => $title,
            'message' => $message,
            'button_link' => $button_link,
            'button_message' => $button_message
        ];

        if($button_link != null || $button_message != null){
            $this->template = $this->template."_button";
        }

        if($file != null){
            if(file_exists($file)){
                $this->attach($file);
            }
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('front.email.'.$this->template)->with(["data" => $this->data]);
    }
}
