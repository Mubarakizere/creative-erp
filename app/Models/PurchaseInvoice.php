<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use App\Traits\LogsActivity;
use Illuminate\Support\Str;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped, LogsActivity;

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