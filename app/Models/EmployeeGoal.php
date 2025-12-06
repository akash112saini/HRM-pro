<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGoal extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'appraisal_cycle_id',
        'title',
        'description',
        'target_value',
        'achieved_value',
        'weightage',
        'status',
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
