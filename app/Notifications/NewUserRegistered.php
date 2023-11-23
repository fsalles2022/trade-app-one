<?php

namespace TradeAppOne\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification
{
    use Queueable;
    private $verificationCode;
    private $mailMessage;

    public function __construct($verificationCode)
    {
        $this->mailMessage      = new MailMessage;
        $this->verificationCode = $verificationCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = ['verificationCode' => $this->verificationCode];
        return $this->mailMessage->view('emails.verify', $data);
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
