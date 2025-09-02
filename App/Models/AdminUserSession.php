<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Model;
use Jiny\Admin\App\Models\User;

class AdminUserSession extends Model
{
    protected $table = 'admin_user_sessions';
    
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'login_at',
        'is_active',
        'browser',
        'browser_version',
        'platform',
        'device',
        'two_factor_used',
        'payload'
    ];
    
    protected $casts = [
        'last_activity' => 'datetime',
        'login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_used' => 'boolean',
        'payload' => 'array'
    ];
    
    /**
     * 관련 사용자
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 활성 세션만 조회
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * 비활성 세션만 조회
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    
    /**
     * 세션 생성 또는 업데이트
     */
    public static function track($user, $request, $twoFactorUsed = false)
    {
        $userAgent = $request->userAgent();
        $browserInfo = self::parseBrowserInfo($userAgent);
        
        try {
            return self::updateOrCreate(
            ['session_id' => session()->getId()],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent,
                'last_activity' => now(),
                'login_at' => now(),
                'is_active' => true,
                'browser' => $browserInfo['browser'],
                'browser_version' => $browserInfo['version'],
                'platform' => $browserInfo['platform'],
                'device' => $browserInfo['device'],
                'two_factor_used' => $twoFactorUsed,
                'payload' => json_encode([
                    'referer' => $request->header('Referer'),
                    'accept_language' => $request->header('Accept-Language'),
                ])
            ]
        );
        } catch (\Exception $e) {
            \Log::error('Session tracking failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 세션 활동 업데이트
     */
    public static function updateActivity($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->update(['last_activity' => now()]);
    }
    
    /**
     * 세션 종료
     */
    public static function terminate($sessionId)
    {
        return self::where('session_id', $sessionId)
            ->update(['is_active' => false]);
    }
    
    /**
     * 브라우저 정보 파싱
     */
    private static function parseBrowserInfo($userAgent)
    {
        $browser = 'Unknown';
        $version = '';
        $platform = 'Unknown';
        $device = 'Desktop';
        
        // 브라우저 감지
        if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/OPR/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        }
        
        // 플랫폼 감지
        if (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
            $device = 'Mobile';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
            $device = preg_match('/ipad/i', $userAgent) ? 'Tablet' : 'Mobile';
        }
        
        return [
            'browser' => $browser,
            'version' => $version,
            'platform' => $platform,
            'device' => $device
        ];
    }
    
    /**
     * 오래된 세션 정리
     */
    public static function cleanupOldSessions($hours = 24)
    {
        return self::where('last_activity', '<', now()->subHours($hours))
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}