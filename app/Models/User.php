<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasTenantScope;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasTenantScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'custom_role_id',
        'super_admin_role_id',
        'employee_id',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function assignedRole()
    {
        return $this->belongsTo(Role::class, 'custom_role_id');
    }

    public function superAdminRole()
    {
        return $this->belongsTo(SuperAdminRole::class, 'super_admin_role_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is company admin
     */
    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if super admin has specific permission
     */
    public function hasSuperAdminPermission(string $permission): bool
    {
        if (!$this->isSuperAdmin() || !$this->superAdminRole) {
            return false;
        }

        return $this->superAdminRole->hasPermission($permission);
    }

    /**
     * Password audit logs relationship
     */
    public function passwordAuditLogs()
    {
        return $this->hasMany(\App\Models\PasswordAuditLog::class);
    }

    /**
     * Scope: Active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
