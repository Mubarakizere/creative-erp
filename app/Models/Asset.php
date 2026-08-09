<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CompanyScoped;

class Asset extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $fillable = [
        'company_id',
        'branch_id',
        'department_id',
        'warehouse_id',
        'asset_category_id',
        'asset_number',
        'name',
        'description',
        'serial_number',
        'barcode',
        'purchase_date',
        'in_service_date',
        'purchase_cost',
        'residual_value',
        'useful_life',
        'useful_units',
        'depreciation_method',
        'accumulated_depreciation',
        'current_book_value',
        'status',
        'condition',
        'assigned_user_id',
        'supplier_id',
        'purchase_order_id',
        'purchase_invoice_id',
        'project_id',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'in_service_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'useful_units' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'current_book_value' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function category() { return $this->belongsTo(AssetCategory::class, 'asset_category_id'); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); }
    public function project() { return $this->belongsTo(Project::class); }
    
    public function depreciations() { return $this->hasMany(AssetDepreciation::class); }
    public function assignments() { return $this->hasMany(AssetAssignment::class); }
    public function transfers() { return $this->hasMany(AssetTransfer::class); }
    public function maintenances() { return $this->hasMany(AssetMaintenance::class); }
    public function impairments() { return $this->hasMany(AssetImpairment::class); }
    public function disposals() { return $this->hasMany(AssetDisposal::class); }
}
