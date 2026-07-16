<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentCreated::class, \App\Listeners\LogActivityListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentCreated::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentCreated::class, \App\Listeners\RefreshDashboardListener::class);
        
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentUpdated::class, \App\Listeners\LogActivityListener::class);
        
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\LogActivityListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\RefreshDashboardListener::class);
        
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentRestored::class, \App\Listeners\LogActivityListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentRestored::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentPinned::class, \App\Listeners\LogActivityListener::class);
        \Illuminate\Support\Facades\Event::listen(\App\Events\CommentUnpinned::class, \App\Listeners\LogActivityListener::class);
        
        \Illuminate\Support\Facades\Event::listen(\App\Events\MentionDetected::class, \App\Listeners\NotifyMentionedUsersListener::class);
    }
}
