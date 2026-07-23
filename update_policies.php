<?php

$dir = 'app/Policies';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$policies = [
    'SupplierPolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('supplier.view'); }
    public function view(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.view') && $user->company_id === $supplier->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('supplier.create'); }
    public function update(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.update') && $user->company_id === $supplier->company_id; }
    public function delete(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.delete') && $user->company_id === $supplier->company_id; }
}
EOT,
    'PurchaseRequisitionPolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequisitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $pr->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('procurement.create'); }
    public function update(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.create') && $user->company_id === $pr->company_id; }
    public function delete(User $user, PurchaseRequisition $pr) { return current_user_can_delete_pr; }
    public function approve(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.approve') && $user->company_id === $pr->company_id; }
}
EOT,
    'PurchaseOrderPolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('purchase_order.view'); }
    public function view(User $user, PurchaseOrder $po) { return $user->hasPermissionTo('purchase_order.view') && $user->company_id === $po->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('purchase_order.create'); }
    public function approve(User $user, PurchaseOrder $po) { return $user->hasPermissionTo('purchase_order.approve') && $user->company_id === $po->company_id; }
}
EOT,
    'GoodsReceiptPolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, GoodsReceipt $gr) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $gr->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('goods_receipt.create'); }
}
EOT,
    'PurchaseInvoicePolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseInvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, PurchaseInvoice $pi) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $pi->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('procurement.create'); }
}
EOT,
    'SupplierPaymentPolicy.php' => <<<'EOT'
<?php
namespace App\Policies;

use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('supplier_payment.view'); }
    public function view(User $user, SupplierPayment $sp) { return $user->hasPermissionTo('supplier_payment.view') && $user->company_id === $sp->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('supplier_payment.create'); }
}
EOT,
];

foreach ($policies as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Created $filename\n";
}
