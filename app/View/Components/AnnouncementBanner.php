<?php

namespace App\View\Components;

use App\Services\AnnouncementService;
use Illuminate\View\Component;

class AnnouncementBanner extends Component
{
    public $announcements;

    public function __construct(AnnouncementService $service)
    {
        if (auth()->check()) {
            $this->announcements = $service->getVisibleForUser(auth()->user(), 1);
        } else {
            $this->announcements = collect();
        }
    }

    public function render()
    {
        return view('components.announcement-banner');
    }
}
