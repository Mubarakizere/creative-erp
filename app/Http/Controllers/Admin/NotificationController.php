<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Notification::class);
        
        $notifications = $this->notificationService->getNotificationsForUser(
            $request->user(),
            $request->only(['search', 'category', 'priority', 'status'])
        );
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function show(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $this->authorize('view', $notification);

        $this->notificationService->markAsRead($request->user(), $id);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }
        
        return back();
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $this->authorize('update', $notification);
        
        $this->notificationService->markAsRead($request->user(), $id);
        
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAsUnread(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $this->authorize('update', $notification);
        
        $this->notificationService->markAsUnread($request->user(), $id);
        
        return back()->with('success', 'Notification marked as unread.');
    }

    public function markAllAsRead(Request $request)
    {
        $this->authorize('updateAny', Notification::class);
        $this->notificationService->markAllAsRead($request->user());
        
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $this->authorize('delete', $notification);
        
        $this->notificationService->delete($request->user(), $id);
        
        return back()->with('success', 'Notification deleted.');
    }

    public function bulkAction(Request $request)
    {
        $this->authorize('updateAny', Notification::class);
        
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:read,unread,delete'
        ]);

        $this->notificationService->bulkAction($request->user(), $request->ids, $request->action);
        
        return back()->with('success', 'Bulk action completed.');
    }
}
