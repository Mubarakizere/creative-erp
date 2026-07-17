<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\NotificationPreferenceService;
use App\Models\User;

class AppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $category,
        public string $priority = 'Normal',
        public ?string $actionUrl = null,
        public ?string $actionText = null,
        public ?string $icon = null,
        public ?string $color = null,
        public ?int $companyId = null,
        public ?int $branchId = null,
        public ?int $createdById = null
    ) {}

    public function via(object $notifiable): array
    {
        if (!$notifiable instanceof User) {
            return ['database'];
        }

        $preferenceService = app(NotificationPreferenceService::class);
        
        $channels = [];

        if ($preferenceService->shouldSendVia($notifiable, 'database', $this->category)) {
            $channels[] = 'database';
        }

        if ($preferenceService->shouldSendVia($notifiable, 'mail', $this->category)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->title)
            ->line($this->message);

        if ($this->actionUrl && $this->actionText) {
            $message->action($this->actionText, url($this->actionUrl));
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'priority' => $this->priority,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'icon' => $this->icon,
            'color' => $this->color,
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'created_by' => $this->createdById,
        ];
    }
}
