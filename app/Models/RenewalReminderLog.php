<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalReminderLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'sent_date',
        'expiry_date',
        'days_before_expiry',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if reminder was sent today for this tenant
     */
    public static function sentToday(int $tenantId): bool
    {
        return self::where('tenant_id', $tenantId)
            ->where('sent_date', today())
            ->exists();
    }
}
