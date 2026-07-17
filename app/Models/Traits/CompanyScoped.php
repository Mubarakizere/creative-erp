<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

trait CompanyScoped
{
    /**
     * Boot the company scoped trait for a model.
     *
     * @return void
     */
    protected static function bootCompanyScoped()
    {
        static::addGlobalScope('company_scoped', function (Builder $builder) {
            // Avoid running in console (e.g., artisan commands) unless testing
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            // Prevent recursion when resolving user or roles
            static $checkingScope = false;
            
            if ($checkingScope) {
                return;
            }

            try {
                $checkingScope = true;

                // Only apply if user is fully authenticated
                if (Auth::hasUser()) {
                    $user = Auth::user();
                    
                    if ($user && $user->company_id && !$user->hasRole('Super Admin')) {
                        $model = $builder->getModel();
                        $table = $model->getTable();
                        
                        // We check if the column exists statically to avoid querying information_schema
                        // Or simply assume if the trait is applied, the model HAS company_id.
                        // For safety, we can check fillable or casts.
                        $builder->where($table . '.company_id', $user->company_id);
                    }
                }
            } finally {
                $checkingScope = false;
            }
        });
    }
}
