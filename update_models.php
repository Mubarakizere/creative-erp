<?php

$dir = 'app/Models';
$files = scandir($dir);

$models = [
    'SupplierCategory.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class SupplierCategory extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function suppliers() { return $this->hasMany(Supplier::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'Supplier.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['is_preferred' => 'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function category() { return $this->belongsTo(SupplierCategory::class, 'supplier_category_id'); }
    public function contacts() { return $this->hasMany(SupplierContact::class); }
    public function paymentTerm() { return $this->belongsTo(PaymentTerm::class); }
    public function requisitions() { return $this->hasMany(PurchaseRequisition::class); }
    public function quotations() { return $this->hasMany(SupplierQuotation::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
    public function invoices() { return $this->hasMany(PurchaseInvoice::class); }
    public function payments() { return $this->hasMany(SupplierPayment::class); }
    public function performances() { return $this->hasMany(SupplierPerformance::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'SupplierContact.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class SupplierContact extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['is_primary' => 'boolean'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'PurchaseRequisition.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class PurchaseRequisition extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['required_date' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }
    public function items() { return $this->hasMany(PurchaseRequisitionItem::class); }
    public function quotations() { return $this->hasMany(SupplierQuotation::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'PurchaseRequisitionItem.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseRequisitionItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function requisition() { return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
EOT,
    'SupplierQuotation.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class SupplierQuotation extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['expiry_date' => 'date', 'valid_until' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function requisition() { return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id'); }
    public function items() { return $this->hasMany(SupplierQuotationItem::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'SupplierQuotationItem.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplierQuotationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function quotation() { return $this->belongsTo(SupplierQuotation::class, 'supplier_quotation_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
EOT,
    'PurchaseOrder.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['order_date' => 'date', 'delivery_date' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function quotation() { return $this->belongsTo(SupplierQuotation::class, 'supplier_quotation_id'); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
    public function invoices() { return $this->hasMany(PurchaseInvoice::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'PurchaseOrderItem.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
EOT,
    'GoodsReceipt.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class GoodsReceipt extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['receipt_date' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(GoodsReceiptItem::class); }
    public function invoices() { return $this->hasMany(PurchaseInvoice::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'GoodsReceiptItem.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GoodsReceiptItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function goodsReceipt() { return $this->belongsTo(GoodsReceipt::class); }
    public function purchaseOrderItem() { return $this->belongsTo(PurchaseOrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
EOT,
    'PurchaseInvoice.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function goodsReceipt() { return $this->belongsTo(GoodsReceipt::class); }
    public function items() { return $this->hasMany(PurchaseInvoiceItem::class); }
    public function payments() { return $this->hasMany(SupplierPayment::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'PurchaseInvoiceItem.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
EOT,
    'SupplierPayment.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['payment_date' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
    'SupplierPerformance.php' => <<<'EOT'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use Illuminate\Support\Str;

class SupplierPerformance extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $guarded = ['id'];
    protected $casts = ['period_start' => 'date', 'period_end' => 'date'];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}
EOT,
];

foreach ($models as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Updated $filename\n";
}
