<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FnfSettlement extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'resignation_id',
        'pending_salary',
        'leave_encashment',
        'bonus',
        'deductions',
        'total_payable',
        'status',
        'processed_by',
        'processed_at',
        'paid_at',
    ];

    protected $casts = [
        'pending_salary' => 'decimal:2',
        'leave_encashment' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'array',
        'total_payable' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function resignation()
    {
        return $this->belongsTo(Resignation::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
