<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'title',
        'department_id',
        'designation_id',
        'description',
        'requirements',
        'employment_type',
        'experience_required',
        'salary_range_min',
        'salary_range_max',
        'location',
        'status',
        'posted_by',
        'posted_at',
        'closing_date',
    ];

    protected $casts = [
        'salary_range_min' => 'decimal:2',
        'salary_range_max' => 'decimal:2',
        'posted_at' => 'datetime',
        'closing_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
