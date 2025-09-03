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
        if (!$this->confirm('모든 비밀번호 시도 기록을 초기화하시겠습니까?')) {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        $count = AdminPasswordLog::count();
        AdminPasswordLog::truncate();
        
        $this->info("총 {$count}개의 비밀번호 시도 기록이 초기화되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => 'ALL',
            'reset_by' => 'console',
            'count' => $count,
            'command' => 'admin:password-reset --all'
        ]);
        
        return 0;
    }
    
    /**
     * 이메일로 시도 초기화
     */
    protected function resetByEmail($email)
    {
        $logs = AdminPasswordLog::where('email', $email)->get();
        
        if ($logs->isEmpty()) {
            $this->error("이메일 '{$email}'에 대한 시도 기록이 없습니다.");
            return 1;
        }
        
        $count = $logs->count();
        AdminPasswordLog::where('email', $email)->delete();
        
        $this->info("✓ {$email}의 {$count}개 시도 기록이 초기화되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => $email,
            'reset_by' => 'console',
            'count' => $count,
            'command' => "admin:password-reset {$email}"
        ]);
        
        return 0;
    }
    
    /**
     * IP로 시도 초기화
     */
    protected function resetByIp($ip)
    {
        $logs = AdminPasswordLog::where('ip_address', $ip)->get();
        
        if ($logs->isEmpty()) {
            $this->error("IP '{$ip}'에 대한 시도 기록이 없습니다.");
            return 1;
        }
        
        $count = $logs->count();
        $emails = $logs->pluck('email')->unique()->implode(', ');
        AdminPasswordLog::where('ip_address', $ip)->delete();
        
        $this->info("✓ IP {$ip}의 {$count}개 시도 기록이 초기화되었습니다.");
        $this->info("  영향받은 이메일: {$emails}");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'ip_address' => $ip,
            'emails' => $emails,
            'reset_by' => 'console',
            'count' => $count,
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
        
        $logs = AdminPasswordLog::where('last_attempt_at', '<', $date)->get();
        
        if ($logs->isEmpty()) {
            $this->info("{$days}일 이전의 시도 기록이 없습니다.");
            return 0;
        }
        
        if (!$this->confirm("{$days}일 이전의 {$logs->count()}개 기록을 초기화하시겠습니까?")) {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        $count = $logs->count();
        AdminPasswordLog::where('last_attempt_at', '<', $date)->delete();
        
        $this->info("✓ {$days}일 이전의 {$count}개 시도 기록이 초기화되었습니다.");
        
        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'days_old' => $days,
            'reset_by' => 'console',
            'count' => $count,
            'command' => "admin:password-reset --days={$days}"
        ]);
        
        return 0;
    }
}