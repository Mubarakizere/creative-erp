<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $newPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Password Has Been Reset')
                    ->greeting("Hello {$notifiable->first_name},")
                    ->line('An administrator has reset your password.')
                    ->line('Your new temporary password is: ' . $this->newPassword)
                    ->action('Login Here', url('/login'))
                    ->line('Please change your password immediately after logging in for security reasons.');
    }
}
