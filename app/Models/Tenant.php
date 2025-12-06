<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subscription_plan',
        'subscription_expires_at',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'contact_email',
        'contact_phone',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Check if subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription_expires_at) {
            return true; // No expiry set
        }

        return $this->subscription_expires_at->isFuture();
    }

    /**
     * Get subscription plan features.
     */
    public function getFeatures(): array
    {
        $plans = config('tenant.subscription_plans');
        return $plans[$this->subscription_plan]['features'] ?? [];
    }

    /**
     * Check if tenant has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->getFeatures();
        return in_array('all', $features) || in_array($feature, $features);
    }

    /**
     * Run a callback in the tenant's database context.
     */
    public function runOnTenant(callable $callback)
    {
        $originalConnection = \Illuminate\Support\Facades\DB::getDefaultConnection();
        $tenantConnectionName = 'tenant';

        // Configure the tenant connection
        $databaseName = 'hrmpro_tenant_' . $this->id;
        config(['database.connections.tenant.database' => $databaseName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');
        \Illuminate\Support\Facades\DB::setDefaultConnection('tenant');

        try {
            return $callback();
        } finally {
            // Restore original connection
            \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
        }
    }
}

