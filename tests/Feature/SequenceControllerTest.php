<?php

namespace Tests\Feature;

use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Sequence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'settings.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'settings.manage', 'guard_name' => 'web']);
        $role->givePermissionTo(['settings.view', 'settings.manage']);

        $this->admin = User::factory()->create(['company_id' => $this->company->id]);
        $this->admin->assignRole('Admin');
    }

    public function test_admin_can_update_sequences(): void
    {
        $sequence = Sequence::create([
            'company_id' => $this->company->id,
            'document_type' => 'invoice',
            'prefix' => 'INV-',
            'padding' => 6,
            'next_number' => 1,
            'active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.sequences.update'), [
            'sequences' => [
                [
                    'id' => $sequence->id,
                    'prefix' => 'INVOICE-',
                    'next_number' => 100,
                    'padding' => 5,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sequences', [
            'id' => $sequence->id,
            'prefix' => 'INVOICE-',
            'next_number' => 100,
            'padding' => 5,
        ]);
    }

    public function test_cannot_update_other_company_sequences(): void
    {
        $otherCompany = Company::factory()->create();
        $sequence = Sequence::create([
            'company_id' => $otherCompany->id,
            'document_type' => 'invoice',
            'prefix' => 'INV-',
            'padding' => 6,
            'next_number' => 1,
            'active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.sequences.update'), [
            'sequences' => [
                [
                    'id' => $sequence->id,
                    'prefix' => 'INVOICE-',
                    'next_number' => 100,
                    'padding' => 5,
                ]
            ]
        ]);

        $response->assertStatus(404);
    }
}
