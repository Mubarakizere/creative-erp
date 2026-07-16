<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meeting;
use App\Models\Company;
use App\Models\User;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::with('branches', 'projects')->get();
        
        foreach ($companies as $company) {
            $users = User::where('company_id', $company->id)->get();
            if ($users->count() < 2) continue;

            $branch = $company->branches->first();
            $projects = $company->projects;

            if (!$branch) continue;

            // Create 5-10 meetings per company
            $meetingCount = rand(5, 10);
            
            for ($i = 0; $i < $meetingCount; $i++) {
                $organizer = $users->random();
                $project = $projects->count() > 0 && rand(0, 1) ? $projects->random() : null;
                
                $meeting = Meeting::factory()->create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'project_id' => $project?->id,
                    'created_by' => $organizer->id,
                    'updated_by' => $organizer->id,
                ]);

                // Attach 2-5 attendees
                $attendees = $users->where('id', '!=', $organizer->id)->random(min(rand(1, 4), $users->count() - 1));
                
                // Organizer is always an attendee
                $meeting->attendees()->attach($organizer->id, ['attendance_status' => 'accepted']);
                
                foreach ($attendees as $attendee) {
                    $status = collect(['accepted', 'tentative', 'declined', 'pending'])->random();
                    $meeting->attendees()->attach($attendee->id, ['attendance_status' => $status]);
                }
            }
        }
    }
}
