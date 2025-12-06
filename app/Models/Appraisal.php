<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'appraisal_cycle_id',
        'self_rating',
        'manager_rating',
        'final_rating',
        'self_comments',
        'manager_comments',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'self_rating' => 'decimal:2',
        'manager_rating' => 'decimal:2',
        'final_rating' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function appraisalCycle()
    {
        return $this->belongsTo(AppraisalCycle::class);
    }
}
