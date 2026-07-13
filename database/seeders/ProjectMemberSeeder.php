<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use App\Models\Department;
use App\Models\ProjectMember;

class ProjectMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();
        $departments = Department::all();

        if ($projects->isEmpty() || $users->isEmpty() || $departments->isEmpty()) {
            return;
        }

        foreach ($projects as $project) {
            // Assign 1 Project Manager
            $pmUser = $users->random();
            $dept = $departments->random();
            
            $project->projectMembers()->create([
                'user_id' => $pmUser->id,
                'department_id' => $dept->id,
                'project_role' => 'Project Manager',
                'allocation_percentage' => 100,
                'joined_at' => $project->created_at ?? now()->subMonths(6),
                'status' => 'Active',
            ]);
            
            $project->update(['project_manager_id' => $pmUser->id]);
            
            // Assign 4-9 other members
            $numMembers = rand(4, 9);
            $assignedUserIds = [$pmUser->id];
            
            $roles = [
                'Assistant Project Manager', 'Architect', 'Engineer', 'Site Engineer', 
                'Civil Engineer', 'Electrical Engineer', 'Mechanical Engineer', 
                'Quantity Surveyor', 'Procurement Officer', 'Accountant', 
                'HR Representative', 'Quality Controller', 'Safety Officer', 
                'Supervisor', 'Foreman', 'Technician'
            ];

            for ($i = 0; $i < $numMembers; $i++) {
                $user = $users->whereNotIn('id', $assignedUserIds)->random();
                $assignedUserIds[] = $user->id;
                
                $project->projectMembers()->create([
                    'user_id' => $user->id,
                    'department_id' => $departments->random()->id,
                    'project_role' => $roles[array_rand($roles)],
                    'allocation_percentage' => rand(10, 100),
                    'hourly_rate' => rand(20, 150),
                    'joined_at' => $project->created_at ?? now()->subMonths(6),
                    'status' => rand(1, 10) > 8 ? 'Inactive' : 'Active', // 20% chance of being inactive
                ]);
            }
        }
    }
}
