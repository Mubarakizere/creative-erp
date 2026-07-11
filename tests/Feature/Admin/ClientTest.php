<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
    }

    public function test_can_view_clients_index(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.clients.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    public function test_can_create_client(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.clients.store'), [
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'client_type' => 'Company',
            'company_name' => 'Acme Corp',
            'phone' => '1234567890',
            'email' => 'acme@example.com',
            'status' => 'active',
        ]);

        $client = Client::where('company_name', 'Acme Corp')->first();
        
        $response->assertRedirect(route('admin.clients.show', $client));
        $this->assertDatabaseHas('clients', [
            'company_name' => 'Acme Corp',
            'client_type' => 'Company',
        ]);
    }
}
