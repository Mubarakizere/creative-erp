<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class QuotationCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->company = Company::factory()->create();
        
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->user->assignRole($role);
    }

    public function test_can_create_quotation_manually()
    {
        $payload = [
            'reference' => 'REF-001',
            'valid_until' => now()->addDays(30)->toDateString(),
            'notes' => 'Manual quotation',
            'items' => [
                [
                    'product_name' => 'Consulting Services',
                    'quantity' => 10,
                    'unit_price' => 150,
                    'discount' => 0,
                ],
                [
                    'product_name' => 'Software License',
                    'quantity' => 1,
                    'unit_price' => 5000,
                    'discount' => 500,
                    'discount_type' => 'fixed',
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('admin.crm.quotations.store'), $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('quotations', [
            'reference' => 'REF-001',
            'company_id' => $this->company->id,
            'subtotal' => 6000, // 1500 + 4500 (after discount)
            'total_discount' => 500,
            'grand_total' => 6000, // Same as subtotal without tax
        ]);

        $quotation = Quotation::where('reference', 'REF-001')->first();
        $this->assertCount(2, $quotation->items);
    }

    public function test_can_create_quotation_from_opportunity()
    {
        $opportunity = Opportunity::create(['company_id' => $this->company->id, 'name' => 'Test Opportunity']);

        $payload = [
            'opportunity_id' => $opportunity->id,
            'items' => [
                [
                    'product_name' => 'Opp Item',
                    'quantity' => 1,
                    'unit_price' => 1000,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('admin.crm.quotations.store'), $payload);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('quotations', [
            'opportunity_id' => $opportunity->id,
            'grand_total' => 1000,
        ]);
    }

    public function test_can_create_quotation_from_account()
    {
        $account = Account::create(['company_id' => $this->company->id, 'name' => 'Test Account']);

        $payload = [
            'account_id' => $account->id,
            'items' => [
                [
                    'product_name' => 'Account Item',
                    'quantity' => 2,
                    'unit_price' => 2000,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('admin.crm.quotations.store'), $payload);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('quotations', [
            'account_id' => $account->id,
            'grand_total' => 4000,
        ]);
        
        // Verify customer information is populated correctly via the relation
        $quotation = Quotation::where('account_id', $account->id)->first();
        $this->assertEquals($account->id, $quotation->account->id);
    }

    public function test_can_save_edit_duplicate_archive_and_restore_quotation()
    {
        // 1. Save (Create)
        $payload = [
            'reference' => 'LIFECYCLE-1',
            'items' => [
                [
                    'product_name' => 'Item 1',
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ];

        $createResponse = $this->actingAs($this->user)->post(route('admin.crm.quotations.store'), $payload);
        $createResponse->assertStatus(302);
        $quotationId = Quotation::where('reference', 'LIFECYCLE-1')->first()->id;
        
        $this->assertDatabaseHas('quotations', ['id' => $quotationId, 'reference' => 'LIFECYCLE-1']);

        // 2. Edit
        $editPayload = [
            'reference' => 'LIFECYCLE-2',
            'items' => [
                [
                    'product_name' => 'Item 2',
                    'quantity' => 2,
                    'unit_price' => 200,
                ]
            ]
        ];

        $editResponse = $this->actingAs($this->user)->put(route('admin.crm.quotations.update', $quotationId), $editPayload);
        $editResponse->assertStatus(302);
        
        $this->assertDatabaseHas('quotations', ['id' => $quotationId, 'reference' => 'LIFECYCLE-2']);
        $this->assertDatabaseMissing('quotation_items', ['product_name' => 'Item 1']); // Old items should be gone or updated
        $this->assertDatabaseHas('quotation_items', ['quotation_id' => $quotationId, 'product_name' => 'Item 2']);

        // 3. Duplicate
        $duplicateResponse = $this->actingAs($this->user)->post(route('admin.crm.quotations.duplicate', $quotationId));
        $duplicateResponse->assertStatus(302);
        
        // Find the duplicated quotation (same reference but different ID)
        $duplicatedId = Quotation::where('reference', 'LIFECYCLE-2')->where('id', '!=', $quotationId)->first()->id;

        $this->assertNotEquals($quotationId, $duplicatedId);
        $this->assertDatabaseHas('quotations', ['id' => $duplicatedId, 'reference' => 'LIFECYCLE-2']);
        $this->assertDatabaseHas('quotation_items', ['quotation_id' => $duplicatedId, 'product_name' => 'Item 2']);

        // 4. Archive (Soft Delete)
        $archiveResponse = $this->actingAs($this->user)->delete(route('admin.crm.quotations.destroy', $quotationId));
        $archiveResponse->assertStatus(302);

        $this->assertSoftDeleted('quotations', ['id' => $quotationId]);

        // 5. Restore
        $restoreResponse = $this->actingAs($this->user)->patch(route('admin.crm.quotations.restore', $quotationId));
        $restoreResponse->assertStatus(302);

        $this->assertNotSoftDeleted('quotations', ['id' => $quotationId]);
    }
}
