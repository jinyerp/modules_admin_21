<?php

namespace Jiny\Admin\App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AdminUserLog extends Model
{
    protected $table = 'admin_user_logs';
    
    protected $fillable = [
        'user_id',
        'email',
        'name',
        'action',
        'ip_address',
        'user_agent',
        'details',
        'session_id',
        'logged_at'
    ];
    
    protected $casts = [
        'details' => 'array',
        'logged_at' => 'datetime',
    ];
    
    /**
     * 관련 사용자
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 로그 기록
     */
    public static function log($action, $user = null, $details = [])
    {
        $request = request();
        
        $data = [
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'logged_at' => now(),
            'details' => $details
        ];
        
        if ($user) {
            $data['user_id'] = $user->id;
            $data['email'] = $user->email;
            $data['name'] = $user->name;
        } elseif ($action === 'failed_login') {
            // 실패한 로그인의 경우 입력된 이메일 저장
            $data['email'] = $request->input('email', 'unknown');
        }
        
        return static::create($data);
    }
    
    /**
     * 액션 레이블 가져오기
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'login' => '로그인',
            'logout' => '로그아웃',
            'failed_login' => '로그인 실패',
            'password_reset' => '비밀번호 재설정',
            'profile_update' => '프로필 수정',
            'unauthorized_access' => '권한 없는 접근'
        ];
        
        return $labels[$this->action] ?? $this->action;
    }
    
    /**
     * 액션 색상 가져오기
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'login' => 'green',
            'logout' => 'blue',
            'failed_login' => 'red',
            'password_reset' => 'yellow',
            'profile_update' => 'indigo',
            'unauthorized_access' => 'red'
        ];
        
        return $colors[$this->action] ?? 'gray';
    }
}