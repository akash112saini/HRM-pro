<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait HasTenantScope
{
    /**
     * Boot the tenant scope trait for a model.
     */
    protected static function bootHasTenantScope(): void
    {
        // static::addGlobalScope(new TenantScope); // Global scope removed for database separation

        static::creating(function (Model $model) {
            if (!$model->getAttribute(config('tenant.tenant_column'))) {
                $model->setAttribute(
                    config('tenant.tenant_column'),
                    app()->bound('tenant.id') ? app('tenant.id') : null
                );
            }
        });
    }

    /**
     * Get the tenant that owns the model.
     */
    public function tenant()
    {
        return $this->belongsTo(config('tenant.tenant_model'), config('tenant.tenant_column'));
    }

    /**
     * Scope a query to exclude tenant filtering.
     * Deprecated: No longer needed with database separation.
     */
    public function scopeWithoutTenantScope($query)
    {
        return $query;
    }

    /**
     * Scope a query to a specific tenant.
     * Deprecated: No longer needed with database separation.
     */
    public function scopeForTenant($query, $tenantId)
    {
        // For backward compatibility, we might keep this but it won't do cross-database queries easily
        // If we are already in the tenant database, this is redundant unless checking the tenant_id column
        return $query->where(config('tenant.tenant_column'), $tenantId);
    }
}
