<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'resignation_date',
        'last_working_day',
        'reason',
        'manager_status',
        'manager_approved_by',
        'manager_approved_at',
        'manager_remarks',
        'admin_status',
        'admin_approved_by',
        'admin_approved_at',
        'admin_remarks',
        'final_status',
    ];

    protected $casts = [
        'resignation_date' => 'date',
        'last_working_day' => 'date',
        'manager_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    // Scopes
    public function scopePendingManagerApproval($query)
    {
        return $query->where('manager_status', 'pending');
    }

    public function scopePendingAdminApproval($query)
    {
        return $query->where('manager_status', 'approved')
            ->where('admin_status', 'pending');
    }

    public function scopeFinalApproved($query)
    {
        return $query->where('final_status', 'approved');
    }
}
