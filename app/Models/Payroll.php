<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'month',
        'year',
        'salary_structure_id',
        'basic_salary',
        'gross_salary',
        'net_salary',
        'earnings',
        'deductions',
        'total_working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'loss_of_pay_amount',
        'overtime_amount',
        'overtime_hours',
        'tax_deducted',
        'status',
        'processed_at',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'remarks',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'earnings' => 'array',
        'deductions' => 'array',
        'loss_of_pay_amount' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'tax_deducted' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
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

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    /**
     * Scopes
     */
    public function scopeForPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }
}
