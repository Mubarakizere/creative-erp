<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $fillable = [
        'company_id',
        'quotation_number',
        'reference',
        'lead_id',
        'opportunity_id',
        'account_id',
        'contact_id',
        'status_id',
        'template_id',
        'payment_term_id',
        'owner_id',
        'currency',
        'valid_until',
        'subtotal',
        'total_discount',
        'total_tax',
        'grand_total',
        'notes',
        'terms',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function opportunity() { return $this->belongsTo(Opportunity::class); }
    public function account() { return $this->belongsTo(Account::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function status() { return $this->belongsTo(QuotationStatus::class, 'status_id'); }
    public function template() { return $this->belongsTo(QuotationTemplate::class, 'template_id'); }
    public function paymentTerm() { return $this->belongsTo(PaymentTerm::class, 'payment_term_id'); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function items() { return $this->hasMany(QuotationItem::class)->orderBy('sort_order'); }
    public function approvals() { return $this->hasMany(QuotationApproval::class); }
    
    // Poly-morphic relations for ERP integrations
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
    public function comments() { return $this->morphMany(Comment::class, 'commentable'); }
    public function activities() { return $this->morphMany(Activity::class, 'activityable'); }
    // Approvals standard MorphTo
    public function workflowApprovals() { return $this->morphMany(Approval::class, 'approvable'); }
}
