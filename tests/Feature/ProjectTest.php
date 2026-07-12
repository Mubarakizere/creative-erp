<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Company $company;
    protected Branch $branch;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->company = Company::factory()->create();
        $this->branch = Branch::factory()->create(['company_id' => $this->company->id]);
        $this->client = Client::factory()->create(['company_id' => $this->company->id, 'branch_id' => $this->branch->id]);
    }

    public function test_can_view_projects_list()
    {
        Project::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.index'));

        $response->assertStatus(200);
        $response->assertViewHas('projects');
    }

    public function test_can_create_project()
    {
        $manager = User::factory()->create();

        $data = [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'client_id' => $this->client->id,
            'project_manager_id' => $manager->id,
            'project_code' => 'PRJ-100',
            'name' => 'New Office Build',
            'priority' => 'High',
            'status' => 'Planning',
            'currency' => 'USD',
            'start_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.projects.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['project_code' => 'PRJ-100']);
    }

    public function test_can_update_project()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $data = $project->toArray();
        $data['name'] = 'Updated Project Name';

        $response = $this->actingAs($this->admin)->put(route('admin.projects.update', $project), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name'
        ]);
    }

    public function test_can_archive_project()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.projects.destroy', $project));

        $response->assertRedirect();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_can_restore_project()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $project->delete();

        $response = $this->actingAs($this->admin)->patch(route('admin.projects.restore', $project->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'deleted_at' => null]);
    }

    public function test_can_close_project()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id, 'status' => 'In Progress']);

        $response = $this->actingAs($this->admin)->patch(route('admin.projects.close', $project));

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'Closed', 'progress' => 100]);
    }

    public function test_can_duplicate_project()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.projects.duplicate', $project));

        $response->assertRedirect();
        
        $this->assertDatabaseCount('projects', 2);
    }
}
