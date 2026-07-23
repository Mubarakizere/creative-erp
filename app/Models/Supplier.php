<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use App\Traits\LogsActivity;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped, LogsActivity;

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