<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'changed_by',
        'ip_address',
        'user_agent',
        'action',
        'notes',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Log a password change
     */
    public static function logChange(User $user, ?User $changedBy = null, string $action = 'changed', ?string $notes = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'changed_by' => $changedBy?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $action,
            'notes' => $notes,
            'changed_at' => now(),
        ]);
    }
}
