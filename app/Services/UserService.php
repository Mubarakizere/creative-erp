<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AccountActivated;
use App\Notifications\AccountDeactivated;
use App\Notifications\PasswordReset;
use App\Notifications\WelcomeEmail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Auth\Events\Registered;

class UserService
{
    /**
     * Get a paginated list of users based on filters.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['company', 'branch', 'department', 'roles']);

        // Only super admin can see super admins or soft deleted users generally, 
        // but for now we apply generic filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['search']}%")
                  ->orWhere('last_name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['trashed']) && $filters['trashed']) {
            $query->onlyTrashed();
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $password = $data['password'] ?? Str::random(12);

            $user = User::create([
                'company_id' => $data['company_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'password' => Hash::make($password),
                'status' => $data['status'] ?? 'pending',
                'created_by' => auth()->id(),
            ]);

            if (!empty($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $this->uploadAvatar($user, $data['avatar']);
            }

            // Fire registered event to send default verification email if needed
            event(new Registered($user));
            
            // Custom welcome email if user is created active or generated pass
            if ($user->isActive()) {
                $user->notify(new WelcomeEmail($password));
            }

            return $user;
        });
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'company_id' => $data['company_id'] ?? $user->company_id,
                'branch_id' => $data['branch_id'] ?? $user->branch_id,
                'department_id' => $data['department_id'] ?? $user->department_id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'status' => $data['status'] ?? $user->status,
                'updated_by' => auth()->id(),
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $oldStatus = $user->status;
            $user->update($updateData);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $this->uploadAvatar($user, $data['avatar']);
            }

            // Status change notifications
            if ($oldStatus !== 'active' && $user->status === 'active') {
                $user->notify(new AccountActivated());
            } elseif ($oldStatus === 'active' && in_array($user->status, ['inactive', 'suspended'])) {
                $user->notify(new AccountDeactivated());
            }

            return $user;
        });
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Restore a soft deleted user.
     */
    public function restore(User $user): bool
    {
        return $user->restore();
    }

    /**
     * Activate a user.
     */
    public function activate(User $user): bool
    {
        $updated = $user->update([
            'status' => 'active',
            'updated_by' => auth()->id(),
        ]);

        if ($updated) {
            $user->notify(new AccountActivated());
        }

        return $updated;
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user): bool
    {
        $updated = $user->update([
            'status' => 'inactive',
            'updated_by' => auth()->id(),
        ]);

        if ($updated) {
            $user->notify(new AccountDeactivated());
        }

        return $updated;
    }

    /**
     * Manually reset user password and notify them.
     */
    public function resetPassword(User $user): bool
    {
        $newPassword = Str::random(12);

        $updated = $user->update([
            'password' => Hash::make($newPassword),
            'updated_by' => auth()->id(),
        ]);

        if ($updated) {
            $user->notify(new PasswordReset($newPassword));
        }

        return $updated;
    }

    /**
     * Upload and update user avatar.
     */
    public function uploadAvatar(User $user, UploadedFile $file): void
    {
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
    }
}
