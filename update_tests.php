<?php

$dir = 'tests/Feature/Procurement';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$tests = [
    'ProcurementFlowTest.php' => <<<'EOT'
<?php

namespace Tests\Feature\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;

class ProcurementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        // Give permissions
        $permissions = [
            'supplier.create', 'supplier.view', 
            'procurement.create', 'procurement.view', 'procurement.approve',
            'purchase_order.create', 'purchase_order.view', 'purchase_order.approve',
            'goods_receipt.create',
            'supplier_payment.create', 'supplier_payment.view'
        ];
        
        foreach ($permissions as $p) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            $this->user->givePermissionTo($p);
        }
    }

    public function test_supplier_creation()
    {
        $response = $this->actingAs($this->user)->postJson('/api/procurement/suppliers', [
            'name' => 'Test Supplier',
            'code' => 'SUP-001',
            'email' => 'supplier@test.com'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-001']);
    }

    public function test_purchase_requisition_creation()
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->postJson('/api/procurement/purchase-requisitions', [
            'code' => 'PR-001',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('purchase_requisitions', ['code' => 'PR-001']);
    }
}
EOT,
];

foreach ($tests as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Created $filename\n";
}
