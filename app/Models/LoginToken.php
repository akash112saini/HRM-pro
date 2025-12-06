<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoginToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'used',
        'expires_at',
        'used_at',
        'used_from_ip',
        'created_by',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Creator relationship
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a new login token
     */
    public static function generate(User $user, int $hoursValid = 24)
    {
        return self::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addHours($hoursValid),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(string $ip = null)
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
            'used_from_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Scope: Valid tokens only
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)
            ->where('expires_at', '>', now());
    }
}
