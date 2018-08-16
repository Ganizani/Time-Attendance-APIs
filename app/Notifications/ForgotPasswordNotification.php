<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ForgotPasswordNotification extends Notification
{
    use Queueable;

    private $user, $url, $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $token){

        $this->user  = $user;
        $this->token = $token;
        $this->url   = url(env('APP_URL' ));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Password Reset")
            ->line("Hi {$this->user->title} {$this->user->first_name} {$this->user->last_name},")
            ->line("We received a request to reset your password. Click the link below to choose a new one.")
            ->action("Reset Your Password","{$this->url}/password/reset?token={$this->token}")
            ->line("Thank you for using our Application!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
