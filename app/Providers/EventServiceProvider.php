<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\NotificationEventSubscriber;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::subscribe(NotificationEventSubscriber::class);
        Event::listen(\App\Events\WorkflowApproved::class, \App\Listeners\UpdateApprovableStatus::class);
        Event::listen(\App\Events\WorkflowRejected::class, \App\Listeners\UpdateApprovableStatus::class);
        Event::listen(\App\Events\InventoryUpdated::class, \App\Listeners\GenerateInventoryJournalEntry::class);
    }
}
