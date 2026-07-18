<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Announcement $announcement
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Integration with existing NotificationPreferenceService can be done here.
        // For now, default to database (in-app). Can be expanded to 'mail' based on user preference.
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Announcement: {$this->announcement->title}")
                    ->line("A new {$this->announcement->priority} priority announcement has been published.")
                    ->line($this->announcement->title)
                    ->action('View Announcement', url("/announcements/{$this->announcement->id}"))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'category' => $this->announcement->category,
            'priority' => $this->announcement->priority,
        ];
    }
}
