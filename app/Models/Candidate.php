<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends TenantBaseModel
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'job_posting_id',
        'name',
        'email',
        'phone',
        'resume_path',
        'current_stage',
        'source',
        'applied_at',
        'notes',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function scopeInStage($query, $stage)
    {
        return $query->where('current_stage', $stage);
    }
}
