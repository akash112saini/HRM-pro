<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'date',
        'shift_id',
        'punch_in',
        'punch_out',
        'punch_in_device_id',
        'punch_out_device_id',
        'punch_in_location',
        'punch_out_location',
        'status',
        'total_work_hours',
        'is_late',
        'late_minutes',
        'is_early_departure',
        'early_departure_minutes',
        'overtime_hours',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'punch_in' => 'datetime',
        'punch_out' => 'datetime',
        'punch_in_location' => 'array',
        'punch_out_location' => 'array',
        'total_work_hours' => 'decimal:2',
        'is_late' => 'boolean',
        'is_early_departure' => 'boolean',
        'overtime_hours' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function correction()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }
}
