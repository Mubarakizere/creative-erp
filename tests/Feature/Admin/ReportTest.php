<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\ReportTemplate;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->company = Company::factory()->create();
        
        $this->adminUser = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        $adminRole = Role::where('name', 'Company Admin')->first();
        $this->adminUser->assignRole($adminRole);
    }

    public function test_admin_can_view_reports_dashboard()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
    }

    public function test_admin_can_create_report_template()
    {
        $payload = [
            'name' => 'My Custom Project Report',
            'description' => 'A test report',
            'type' => 'project_summary',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.reports.store'), $payload);

        $this->assertDatabaseHas('report_templates', [
            'name' => 'My Custom Project Report',
            'type' => 'project_summary',
            'company_id' => $this->company->id,
            'created_by' => $this->adminUser->id,
            'is_system' => false,
        ]);

        $template = ReportTemplate::where('name', 'My Custom Project Report')->first();
        $response->assertRedirect(route('admin.reports.show', $template));
    }

    public function test_admin_can_view_report_viewer()
    {
        $template = ReportTemplate::create([
            'name' => 'System Project Report',
            'type' => 'project_summary',
            'is_system' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.show', $template));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.viewer');
        $response->assertSee('System Project Report');
    }

    public function test_admin_can_export_report()
    {
        $template = ReportTemplate::create([
            'name' => 'Exportable Report',
            'type' => 'project_summary',
            'is_system' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.reports.export', $template), [
            'format' => 'pdf',
        ]);

        $this->assertDatabaseHas('export_histories', [
            'report_name' => 'Exportable Report',
            'format' => 'pdf',
            'user_id' => $this->adminUser->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_toggle_favorite_report()
    {
        $template = ReportTemplate::create([
            'name' => 'Favorite Report',
            'type' => 'project_summary',
            'is_system' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.reports.favorite', $template));

        $this->assertDatabaseHas('favorite_reports', [
            'user_id' => $this->adminUser->id,
            'report_template_id' => $template->id,
        ]);

        $response->assertRedirect();
    }
}
