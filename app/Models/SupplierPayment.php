<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;
use App\Traits\LogsActivity;
use Illuminate\Support\Str;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped, LogsActivity;

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
    public function invoice() { return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id'); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}