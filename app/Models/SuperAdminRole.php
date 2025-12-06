<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuperAdminRole extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'super_admin_role_id');
    }

    /**
     * Scope: Active roles only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get available permissions
     */
    public static function availablePermissions(): array
    {
        return [
            'manage_tenants' => 'Manage Tenants (Create/Edit/Delete)',
            'view_tenants' => 'View Tenants',
            'manage_subscriptions' => 'Manage Subscriptions',
            'view_subscriptions' => 'View Subscriptions',
            'manage_subscription_plans' => 'Manage Subscription Plans',
            'view_revenue' => 'View Revenue Reports',
            'manage_payments' => 'Manage Payments',
            'manage_passwords' => 'Password Management',
            'manage_super_admins' => 'Manage Super Admin Users',
            'manage_roles' => 'Manage Roles',
            'manage_settings' => 'Manage System Settings',
            'view_activity_logs' => 'View Activity Logs',
        ];
    }
}
