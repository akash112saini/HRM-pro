<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricDevice extends Model
{
    use HasFactory, HasTenantScope;

    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_id',
        'device_id',
        'device_name',
        'location',
        'api_token',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'api_token',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
