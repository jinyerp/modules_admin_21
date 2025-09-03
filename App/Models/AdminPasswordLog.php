<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AdminPasswordLog extends Model
{
    use HasFactory;
    
    protected $table = 'admin_password_logs';
    
    protected $fillable = [
        'email',
        'user_id',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device',
        'attempt_count',
        'first_attempt_at',
        'last_attempt_at',
        'is_blocked',
        'blocked_at',
        'unblocked_at',
        'status',
        'details'
    ];
    
    protected $casts = [
        'details' => 'array',
        'is_blocked' => 'boolean',
        'first_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'blocked_at' => 'datetime',
        'unblocked_at' => 'datetime'
    ];
    
    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 로그인 실패 기록
     */
    public static function recordFailedAttempt($email, $request, $userId = null)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $browserInfo = self::parseBrowserInfo($userAgent);
        
        // 동일한 이메일과 IP로 최근 24시간 내 기록 확인
        $recentLog = self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('last_attempt_at', '>=', now()->subDay())
            ->where('status', '!=', 'resolved')
            ->first();
        
        if ($recentLog) {
            // 기존 기록 업데이트
            $recentLog->attempt_count++;
            $recentLog->last_attempt_at = now();
            
            // 5회 이상 실패 시 차단
            if ($recentLog->attempt_count >= 5 && !$recentLog->is_blocked) {
                $recentLog->is_blocked = true;
                $recentLog->blocked_at = now();
                $recentLog->status = 'blocked';
                
                // 차단 로그 기록
                AdminUserLog::log('password_blocked', null, [
                    'email' => $email,
                    'ip_address' => $ipAddress,
                    'attempts' => $recentLog->attempt_count,
                    'blocked_at' => now()
                ]);
            }
            
            $recentLog->save();
            return $recentLog;
        } else {
            // 새로운 기록 생성
            return self::create([
                'email' => $email,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'browser' => $browserInfo['browser'],
                'platform' => $browserInfo['platform'],
                'device' => $browserInfo['device'],
                'attempt_count' => 1,
                'first_attempt_at' => now(),
                'last_attempt_at' => now(),
                'status' => 'failed',
                'details' => [
                    'referer' => $request->header('Referer'),
                    'accept_language' => $request->header('Accept-Language')
                ]
            ]);
        }
    }
    
    /**
     * 차단 여부 확인
     */
    public static function isBlocked($email, $ipAddress)
    {
        return self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->exists();
    }
    
    /**
     * 차단 해제
     */
    public function unblock()
    {
        $this->is_blocked = false;
        $this->unblocked_at = now();
        $this->status = 'resolved';
        $this->save();
        
        // 차단 해제 로그
        AdminUserLog::log('password_unblocked', null, [
            'email' => $this->email,
            'ip_address' => $this->ip_address,
            'unblocked_at' => now()
        ]);
    }
    
    /**
     * 브라우저 정보 파싱
     */
    private static function parseBrowserInfo($userAgent)
    {
        $browser = 'Unknown';
        $platform = 'Unknown';
        $device = 'Desktop';
        
        // 브라우저 감지
        if (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Edge';
        }
        
        // 플랫폼 감지
        if (preg_match('/Windows/', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac OS/', $userAgent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $platform = 'Android';
            $device = 'Mobile';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $platform = 'iOS';
            $device = preg_match('/iPad/', $userAgent) ? 'Tablet' : 'Mobile';
        }
        
        return [
            'browser' => $browser,
            'platform' => $platform,
            'device' => $device
        ];
    }
    
    /**
     * Scope: 차단된 기록만
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }
    
    /**
     * Scope: 활성 기록만 (24시간 내)
     */
    public function scopeActive($query)
    {
        return $query->where('last_attempt_at', '>=', now()->subDay());
    }
    
    /**
     * Scope: 위험한 시도 (3회 이상)
     */
    public function scopeDangerous($query)
    {
        return $query->where('attempt_count', '>=', 3);
    }
}