<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'useful_life',
        'depreciation_method',
        'asset_account_id',
        'accumulated_depreciation_account_id',
        'depreciation_expense_account_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'asset_account_id');
    }

    public function accumulatedDepreciationAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }

    public function depreciationExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
