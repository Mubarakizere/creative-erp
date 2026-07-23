<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use App\Traits\LogsActivity;
use Illuminate\Support\Str;

class SupplierQuotation extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped, LogsActivity;

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