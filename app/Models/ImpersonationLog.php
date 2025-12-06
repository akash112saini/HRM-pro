<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class ImpersonationLog extends Model
{
    use HasTenantScope;

    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_id',
        'admin_id',
        'user_id',
        'started_at',
        'ended_at',
        'ip_address',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Admin who performed the impersonation
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * User who was impersonated
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if impersonation is active
     */
    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * End the impersonation session
     */
    public function end()
    {
        $this->update(['ended_at' => now()]);
    }
}
