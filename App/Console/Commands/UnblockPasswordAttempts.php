<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Jiny\Admin\App\Models\AdminPasswordLog;
use Jiny\Admin\App\Models\AdminUserLog;

class UnblockPasswordAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:password-unblock 
                            {email? : The email address to unblock}
                            {--ip= : The IP address to unblock}
                            {--all : Unblock all blocked attempts}
                            {--show : Show all blocked attempts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '비밀번호 시도 차단을 해제합니다';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 차단된 목록 보기
        if ($this->option('show')) {
            return $this->showBlockedAttempts();
        }
        
        // 모든 차단 해제
        if ($this->option('all')) {
            return $this->unblockAll();
        }
        
        // 특정 이메일 차단 해제
        if ($email = $this->argument('email')) {
            return $this->unblockByEmail($email);
        }
        
        // 특정 IP 차단 해제
        if ($ip = $this->option('ip')) {
            return $this->unblockByIp($ip);
        }
        
        // 인터랙티브 모드
        return $this->interactiveUnblock();
    }
    
    /**
     * 차단된 시도 목록 표시
     */
    protected function showBlockedAttempts()
    {
        $blocked = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->orderBy('blocked_at', 'desc')
            ->get();
        
        if ($blocked->isEmpty()) {
            $this->info('차단된 로그인 시도가 없습니다.');
            return 0;
        }
        
        $this->table(
            ['ID', '이메일', 'IP 주소', '시도 횟수', '차단 시간', '마지막 시도'],
            $blocked->map(function ($log) {
                return [
                    $log->id,
                    $log->email,
                    $log->ip_address,
                    $log->attempt_count,
                    $log->blocked_at ? $log->blocked_at->format('Y-m-d H:i:s') : '-',
                    $log->last_attempt_at ? $log->last_attempt_at->format('Y-m-d H:i:s') : '-',
                ];
            })
        );
        
        $this->info("총 {$blocked->count()}개의 차단된 시도가 있습니다.");
        
        return 0;
    }
    
    /**
     * 모든 차단 해제
     */
    protected function unblockAll()
    {
        if (!$this->confirm('모든 차단을 해제하시겠습니까?')) {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        $blocked = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        $count = 0;
        foreach ($blocked as $log) {
            $log->unblock();
            $count++;
            $this->line("✓ {$log->email} ({$log->ip_address}) 차단 해제됨");
        }
        
        if ($count > 0) {
            $this->info("총 {$count}개의 차단이 해제되었습니다.");
            
            // 시스템 로그 기록
            AdminUserLog::log('password_unblocked', null, [
                'email' => 'ALL',
                'unblocked_by' => 'console',
                'count' => $count,
                'command' => 'admin:password-unblock --all'
            ]);
        } else {
            $this->info('차단 해제할 항목이 없습니다.');
        }
        
        return 0;
    }
    
    /**
     * 이메일로 차단 해제
     */
    protected function unblockByEmail($email)
    {
        $logs = AdminPasswordLog::where('email', $email)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($logs->isEmpty()) {
            $this->error("이메일 '{$email}'에 대한 차단된 시도가 없습니다.");
            return 1;
        }
        
        foreach ($logs as $log) {
            $log->unblock();
            $this->info("✓ {$log->email} (IP: {$log->ip_address}) 차단이 해제되었습니다.");
        }
        
        $this->info("총 {$logs->count()}개의 차단이 해제되었습니다.");
        
        return 0;
    }
    
    /**
     * IP로 차단 해제
     */
    protected function unblockByIp($ip)
    {
        $logs = AdminPasswordLog::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($logs->isEmpty()) {
            $this->error("IP '{$ip}'에 대한 차단된 시도가 없습니다.");
            return 1;
        }
        
        foreach ($logs as $log) {
            $log->unblock();
            $this->info("✓ {$log->email} (IP: {$log->ip_address}) 차단이 해제되었습니다.");
        }
        
        $this->info("총 {$logs->count()}개의 차단이 해제되었습니다.");
        
        return 0;
    }
    
    /**
     * 인터랙티브 차단 해제
     */
    protected function interactiveUnblock()
    {
        // 차단된 목록 표시
        $this->showBlockedAttempts();
        
        $blocked = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();
        
        if ($blocked->isEmpty()) {
            return 0;
        }
        
        // 선택 옵션 생성
        $choices = [];
        foreach ($blocked as $log) {
            $choices[] = "{$log->email} - {$log->ip_address} (시도: {$log->attempt_count}회)";
        }
        $choices[] = '모두 해제';
        $choices[] = '취소';
        
        $choice = $this->choice(
            '차단을 해제할 항목을 선택하세요',
            $choices,
            count($choices) - 1
        );
        
        if ($choice === '취소') {
            $this->info('취소되었습니다.');
            return 0;
        }
        
        if ($choice === '모두 해제') {
            return $this->unblockAll();
        }
        
        // 선택된 항목 찾기
        $index = array_search($choice, $choices);
        if ($index !== false && isset($blocked[$index])) {
            $log = $blocked[$index];
            $log->unblock();
            $this->info("✓ {$log->email} ({$log->ip_address}) 차단이 해제되었습니다.");
        }
        
        return 0;
    }
    
    /**
     * 만료된 차단 자동 해제 (옵션)
     */
    public function autoUnblockExpired($hours = 24)
    {
        $expired = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->where('blocked_at', '<=', now()->subHours($hours))
            ->get();
        
        $count = 0;
        foreach ($expired as $log) {
            $log->unblock();
            $count++;
        }
        
        if ($count > 0) {
            $this->info("{$hours}시간이 지난 {$count}개의 차단이 자동 해제되었습니다.");
        }
        
        return $count;
    }
}