<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BudgetLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function category()
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getActivityTitleAttribute(): string
    {
        if ($this->task) {
            return ($this->task->task_code ? '[' . $this->task->task_code . '] ' : '') . $this->task->name;
        }

        return $this->activity_name ?? 'General Activity';
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class, 'budget_line_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
