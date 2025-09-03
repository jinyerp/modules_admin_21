<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Jiny\Admin\App\Models\AdminPasswordLog;
use Jiny\Admin\App\Models\AdminUserLog;

class ResetPasswordAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:password-reset 
                            {email? : The email address to reset attempts}
                            {--ip= : The IP address to reset attempts}
                            {--days=1 : Reset attempts older than X days}
                            {--all : Reset all password attempts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '비밀번호 시도 횟수를 초기화합니다';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 모든 시도 초기화
        if ($this->option('all')) {
            return $this->resetAll();
        }
        
        // 특정 이메일 초기화
        if ($email = $this->argument('email')) {
            return $this->resetByEmail($email);
        }
        
        // 특정 IP 초기화
        if ($ip = $this->option('ip')) {
            return $this->resetByIp($ip);
        }
        
        // 오래된 시도 초기화
        return $this->resetOldAttempts();
    }
    
    /**
     * 모든 시도 초기화
     */
    protected function resetAll()
    {
        if (!$this->confirm('모든 차단을 해제하시겠습니까?')) {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        // 차단된 레코드만 찾기
        $blockedLogs = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
            
        if ($blockedLogs->isEmpty()) {
            $this->info("차단된 기록이 없습니다.");
            return 0;
        }
        
        $count = $blockedLogs->count();
        $emails = $blockedLogs->pluck('email')->unique();
        
        // 모든 차단 해제
        AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'status' => 'unblocked'
            ]);
        
        // 각 이메일별로 초기화 로그 생성
        foreach ($emails as $email) {
            $userLog = $blockedLogs->where('email', $email)->first();
            AdminPasswordLog::create([
                'email' => $email,
                'user_id' => $userLog->user_id,
                'ip_address' => $userLog->ip_address,
                'user_agent' => 'System - Password Reset Command',
                'browser' => 'Console',
                'platform' => 'System',
                'device' => 'Server',
                'attempt_count' => 0,
                'first_attempt_at' => now(),
                'last_attempt_at' => now(),
                'is_blocked' => false,
                'status' => 'reset',
                'details' => [
                    'reset_by' => 'console_command',
                    'reset_at' => now(),
                    'reset_all' => true
                ]
            ]);
        }
        
        $this->info("총 {$count}개의 차단이 해제되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => 'ALL',
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => 'admin:password-reset --all'
        ]);
        
        return 0;
    }
    
    /**
     * 이메일로 시도 초기화
     */
    protected function resetByEmail($email)
    {
        // 차단된 레코드만 찾기
        $blockedLogs = AdminPasswordLog::where('email', $email)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($blockedLogs->isEmpty()) {
            $this->error("이메일 '{$email}'에 대한 차단된 기록이 없습니다.");
            return 1;
        }
        
        $count = $blockedLogs->count();
        
        // 차단 해제 (상태만 변경)
        AdminPasswordLog::where('email', $email)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'status' => 'unblocked'
            ]);
        
        // 초기화 로그 생성 (새로운 레코드)
        AdminPasswordLog::create([
            'email' => $email,
            'user_id' => $blockedLogs->first()->user_id,
            'ip_address' => $blockedLogs->first()->ip_address,
            'user_agent' => 'System - Password Reset Command',
            'browser' => 'Console',
            'platform' => 'System',
            'device' => 'Server',
            'attempt_count' => 0,
            'first_attempt_at' => now(),
            'last_attempt_at' => now(),
            'is_blocked' => false,
            'status' => 'reset',
            'details' => [
                'reset_by' => 'console_command',
                'reset_at' => now(),
                'unblocked_count' => $count
            ]
        ]);
        
        $this->info("✓ {$email}의 {$count}개 차단이 해제되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => $email,
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => "admin:password-reset {$email}"
        ]);
        
        return 0;
    }
    
    /**
     * IP로 시도 초기화
     */
    protected function resetByIp($ip)
    {
        // 차단된 레코드만 찾기
        $blockedLogs = AdminPasswordLog::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($blockedLogs->isEmpty()) {
            $this->error("IP '{$ip}'에 대한 차단된 기록이 없습니다.");
            return 1;
        }
        
        $count = $blockedLogs->count();
        $emails = $blockedLogs->pluck('email')->unique();
        $emailList = $emails->implode(', ');
        
        // 차단 해제 (상태만 변경)
        AdminPasswordLog::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'status' => 'unblocked'
            ]);
        
        // 각 이메일별로 초기화 로그 생성
        foreach ($emails as $email) {
            $userLog = $blockedLogs->where('email', $email)->first();
            AdminPasswordLog::create([
                'email' => $email,
                'user_id' => $userLog->user_id,
                'ip_address' => $ip,
                'user_agent' => 'System - Password Reset Command',
                'browser' => 'Console',
                'platform' => 'System',
                'device' => 'Server',
                'attempt_count' => 0,
                'first_attempt_at' => now(),
                'last_attempt_at' => now(),
                'is_blocked' => false,
                'status' => 'reset',
                'details' => [
                    'reset_by' => 'console_command',
                    'reset_at' => now(),
                    'reset_ip' => $ip
                ]
            ]);
        }
        
        $this->info("✓ IP {$ip}의 {$count}개 차단이 해제되었습니다.");
        $this->info("  영향받은 이메일: {$emailList}");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'ip_address' => $ip,
            'emails' => $emailList,
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => "admin:password-reset --ip={$ip}"
        ]);
        
        return 0;
    }
    
    /**
     * 오래된 시도 초기화
     */
    protected function resetOldAttempts()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);
        
        // 오래되고 차단된 기록만 찾기
        $blockedLogs = AdminPasswordLog::where('last_attempt_at', '<', $date)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($blockedLogs->isEmpty()) {
            $this->info("{$days}일 이전의 차단된 기록이 없습니다.");
            return 0;
        }
        
        if (!$this->confirm("{$days}일 이전의 {$blockedLogs->count()}개 차단을 해제하시겠습니까?")) {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        $count = $blockedLogs->count();
        $emails = $blockedLogs->pluck('email')->unique();
        
        // 차단 해제
        AdminPasswordLog::where('last_attempt_at', '<', $date)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'status' => 'unblocked'
            ]);
        
        // 각 이메일별로 초기화 로그 생성
        foreach ($emails as $email) {
            $userLog = $blockedLogs->where('email', $email)->first();
            AdminPasswordLog::create([
                'email' => $email,
                'user_id' => $userLog->user_id,
                'ip_address' => $userLog->ip_address,
                'user_agent' => 'System - Password Reset Command',
                'browser' => 'Console',
                'platform' => 'System',
                'device' => 'Server',
                'attempt_count' => 0,
                'first_attempt_at' => now(),
                'last_attempt_at' => now(),
                'is_blocked' => false,
                'status' => 'reset',
                'details' => [
                    'reset_by' => 'console_command',
                    'reset_at' => now(),
                    'days_old' => $days
                ]
            ]);
        }
        
        $this->info("✓ {$days}일 이전의 {$count}개 차단이 해제되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'days_old' => $days,
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => "admin:password-reset --days={$days}"
        ]);
        
        return 0;
    }
}