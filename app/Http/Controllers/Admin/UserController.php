<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = $this->userService->list($request->only([
            'search', 'company_id', 'branch_id', 'department_id', 'status', 'role', 'trashed'
        ]));

        $companies = Company::orderBy('name')->pluck('name', 'id')->toArray();
        $branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();
        $departments = Department::orderBy('name')->pluck('name', 'id')->toArray();
        $roles = Role::orderBy('name')->pluck('name', 'name')->toArray();

        return view('admin.users.index', compact('users', 'companies', 'branches', 'departments', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        // Super admin sees all roles, others might not, but for now we give all
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('companies', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $user = $this->userService->create($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->load('company', 'branch', 'department', 'roles', 'creator', 'updater');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        
        $branches = Branch::where('company_id', $user->company_id)->where('status', 'active')->orderBy('name')->get();
        $departments = Department::where('branch_id', $user->branch_id)->where('status', 'active')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'companies', 'branches', 'departments', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->userService->update($user, $data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Soft delete the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $this->userService->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $user);

        $this->userService->restore($user);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User restored successfully.');
    }

    /**
     * Activate a user.
     */
    public function activate(User $user): RedirectResponse
    {
        Gate::authorize('activate', $user);

        $this->userService->activate($user);

        return redirect()
            ->back()
            ->with('success', 'User activated successfully.');
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user): RedirectResponse
    {
        Gate::authorize('deactivate', $user);

        $this->userService->deactivate($user);

        return redirect()
            ->back()
            ->with('success', 'User deactivated successfully.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        Gate::authorize('resetPassword', $user);

        $this->userService->resetPassword($user);

        return redirect()
            ->back()
            ->with('success', 'User password has been reset and emailed to them.');
    }

    /**
     * Get branches for a given company (AJAX endpoint for dependent dropdown).
     */
    public function getBranches(Company $company): JsonResponse
    {
        $branches = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($branches);
    }

    /**
     * Get departments for a given branch (AJAX endpoint for dependent dropdown).
     */
    public function getDepartments(Branch $branch): JsonResponse
    {
        $departments = Department::where('branch_id', $branch->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    /**
     * Search active users (AJAX endpoint for select components).
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        $users = User::active()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->limit(50)
            ->get(['id', 'first_name', 'last_name', 'email']);

        // Format for TomSelect
        $formatted = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ];
        });

        return response()->json($formatted);
    }
}
