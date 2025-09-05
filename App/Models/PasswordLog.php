<?php

namespace Jiny\Admin\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'user_id',
        'ip_address',
        'browser',
        'user_agent',
        'platform',
        'device',
        'country_code',
        'attempt_count',
        'status',
        'first_attempt_at',
        'last_attempt_at',
        'blocked_at',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'blocked_at' => 'datetime',
        'resolved_at' => 'datetime',
        'attempt_count' => 'integer',
        'user_id' => 'integer',
        'resolved_by' => 'integer',
    ];

    /**
     * Get the user associated with the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who resolved this log.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope a query to only include blocked IPs.
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Scope a query to only include failed attempts.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include resolved logs.
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope a query to filter by IP address.
     */
    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope a query to filter by email.
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Check if this IP should be blocked based on attempt count.
     */
    public function shouldBeBlocked()
    {
        return $this->attempt_count >= 5 && $this->status !== 'blocked';
    }

    /**
     * Block this IP address.
     */
    public function block()
    {
        $this->update([
            'status' => 'blocked',
            'blocked_at' => now(),
        ]);
    }

    /**
     * Unblock this IP address.
     */
    public function unblock($adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Increment the attempt count.
     */
    public function incrementAttempts()
    {
        $this->increment('attempt_count');
        $this->update(['last_attempt_at' => now()]);

        if ($this->shouldBeBlocked()) {
            $this->block();
        }
    }

    /**
     * Log a new password attempt for an IP/email combination.
     */
    public static function logAttempt($email, $ipAddress, $userAgent = null, $userId = null)
    {
        // Check if there's an existing log for this IP/email combination
        $log = static::where('ip_address', $ipAddress)
            ->where('email', $email)
            ->whereIn('status', ['failed', 'blocked'])
            ->first();

        if ($log) {
            // Update existing log
            $log->incrementAttempts();

            return $log;
        }

        // Parse user agent if provided
        $browserInfo = [];
        if ($userAgent) {
            // Simple browser detection (can be enhanced with a proper parser)
            $browserInfo = [
                'user_agent' => $userAgent,
                'browser' => static::detectBrowser($userAgent),
                'platform' => static::detectPlatform($userAgent),
                'device' => static::detectDevice($userAgent),
            ];
        }

        // Create new log
        return static::create(array_merge([
            'email' => $email,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'attempt_count' => 1,
            'status' => 'failed',
            'first_attempt_at' => now(),
            'last_attempt_at' => now(),
        ], $browserInfo));
    }

    /**
     * Simple browser detection from user agent.
     */
    protected static function detectBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }
        if (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    /**
     * Simple platform detection from user agent.
     */
    protected static function detectPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        }
        if (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    /**
     * Simple device detection from user agent.
     */
    protected static function detectDevice($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        }
        if (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        }
        if (strpos($userAgent, 'iPad') !== false) {
            return 'iPad';
        }
        if (strpos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        }

        return 'Desktop';
    }
}
