<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotificationPreferenceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected NotificationPreferenceService $preferenceService)
    {
    }

    public function index(Request $request)
    {
        $preferences = $this->preferenceService->getPreferences($request->user());
        $this->authorize('view', $preferences);

        return view('admin.notifications.preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        $preferences = $this->preferenceService->getPreferences($request->user());
        $this->authorize('update', $preferences);

        $validated = $request->validate([
            'email' => 'boolean',
            'database' => 'boolean',
            'assignments' => 'boolean',
            'mentions' => 'boolean',
            'workflow' => 'boolean',
            'projects' => 'boolean',
            'documents' => 'boolean',
            'meetings' => 'boolean',
            'system' => 'boolean',
        ]);

        // Convert boolean fields correctly
        $fields = ['email', 'database', 'assignments', 'mentions', 'workflow', 'projects', 'documents', 'meetings', 'system'];
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->boolean($field);
        }

        $this->preferenceService->updatePreferences($request->user(), $data);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
