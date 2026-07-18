<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AnnouncementService $announcementService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $filters = $request->only(['search', 'company_id']);
        $announcements = $this->announcementService->getPaginated($filters);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Announcement::class);
        
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $branches = \App\Models\Branch::where('status', 'active')->orderBy('name')->get(['id', 'company_id', 'name']);
        $departments = \App\Models\Department::where('status', 'active')->orderBy('name')->get(['id', 'branch_id', 'name']);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get(['id', 'name']);
        
        return view('announcements.create', compact('companies', 'branches', 'departments', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementRequest $request)
    {
        $this->authorize('create', Announcement::class);

        $data = $request->validated();
        // Ensure booleans
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published');

        $this->announcementService->create($data, $request->user());

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        
        if ($announcement->audience_type === 'specific_users') {
            $announcement->load('users');
        }
        
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $branches = \App\Models\Branch::where('status', 'active')->orderBy('name')->get(['id', 'company_id', 'name']);
        $departments = \App\Models\Department::where('status', 'active')->orderBy('name')->get(['id', 'branch_id', 'name']);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get(['id', 'name']);
        
        return view('announcements.edit', compact('announcement', 'companies', 'branches', 'departments', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $data = $request->validated();
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published');

        // Check if publishing
        if (!$announcement->is_published && $data['is_published']) {
            $this->authorize('publish', $announcement);
        }

        $this->announcementService->update($announcement, $data, $request->user());

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $this->announcementService->delete($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Publish the specified resource.
     */
    public function publish(Announcement $announcement)
    {
        $this->authorize('publish', $announcement);

        $this->announcementService->publish($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Unpublish the specified resource.
     */
    public function unpublish(Announcement $announcement)
    {
        $this->authorize('publish', $announcement); // Or 'update' depending on policy

        $this->announcementService->unpublish($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement unpublished successfully.');
    }
}
