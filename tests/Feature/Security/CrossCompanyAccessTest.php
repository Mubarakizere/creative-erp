<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class CrossCompanyAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $companyA;
    protected $companyB;
    protected $userA;
    protected $userB;
    protected $invoiceB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::factory()->create();
        $this->companyB = Company::factory()->create();

        $this->userA = User::factory()->create(['company_id' => $this->companyA->id]);
        $this->userB = User::factory()->create(['company_id' => $this->companyB->id]);

        $this->invoiceB = Invoice::factory()->create(['company_id' => $this->companyB->id]);
        
        $role = Role::firstOrCreate(['name' => 'Accountant']);
        $this->userA->assignRole($role);
        $this->userB->assignRole($role);
    }

    public function test_user_cannot_view_other_company_invoice()
    {
        $response = $this->actingAs($this->userA)
            ->get(route('admin.finance.invoices.show', $this->invoiceB));

        $response->assertStatus(404);
    }
    
    public function test_user_cannot_create_invoice_for_other_company()
    {
        $data = Invoice::factory()->raw(['company_id' => $this->companyB->id]);
        
        $response = $this->actingAs($this->userA)
            ->post(route('admin.finance.invoices.store'), $data);
            
        $this->assertDatabaseMissing('invoices', [
            'company_id' => $this->companyB->id,
            'invoice_number' => $data['invoice_number']
        ]);
        
        $this->assertDatabaseHas('invoices', [
            'company_id' => $this->companyA->id,
            'invoice_number' => $data['invoice_number']
        ]);
    }
    
    public function test_user_cannot_update_other_company_invoice()
    {
        $response = $this->actingAs($this->userA)
            ->put(route('admin.finance.invoices.update', $this->invoiceB), [
                'status' => 'Paid'
            ]);
            
        $response->assertStatus(404);
        
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoiceB->id,
            'status' => $this->invoiceB->status // Unchanged
        ]);
    }
    
    public function test_user_cannot_delete_other_company_invoice()
    {
        $response = $this->actingAs($this->userA)
            ->delete(route('admin.finance.invoices.destroy', $this->invoiceB));
            
        $response->assertStatus(404);
        
        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoiceB->id,
            'deleted_at' => null
        ]);
    }
}
