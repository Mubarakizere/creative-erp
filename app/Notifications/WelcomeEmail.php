<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $password
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Creative ERP')
                    ->greeting("Hello {$notifiable->first_name},")
                    ->line('Your account has been created successfully.')
                    ->line('Your temporary password is: ' . $this->password)
                    ->action('Login Here', url('/login'))
                    ->line('Please change your password after logging in.');
    }
}
