<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Jiny\Admin\App\Models\AdminPasswordLog;
use Jiny\Admin\App\Models\AdminUserLog;

/**
 * 비밀번호 시도 횟수 초기화 명령어
 * 
 * 비밀번호 실패로 차단된 사용자나 IP의 시도 횟수를 초기화하고
 * 차단을 해제합니다. 보안 관리를 위한 필수 명령어입니다.
 * 
 * 주요 기능:
 * - 특정 이메일/IP 차단 해제
 * - 오래된 차단 자동 해제
 * - 전체 차단 해제
 * - 모든 해제 작업 로깅
 * 
 * @package Jiny\Admin
 * @author JinyPHP
 * @since 1.0.0
 */
class ResetPasswordAttempts extends Command
{
    /**
     * 콘솔 명령어 시그니처
     * 
     * 사용법: php artisan admin:reset-password-attempts [email] [options]
     * 
     * Arguments:
     *   email : 초기화할 이메일 주소 (선택적)
     * 
     * Options:
     *   --ip : 초기화할 IP 주소
     *   --days : X일 이전의 시도 초기화 (기본값: 1)
     *   --all : 모든 비밀번호 시도 초기화
     *
     * @var string
     */
    protected $signature = 'admin:reset-password-attempts 
                            {email? : 초기화할 이메일 주소}
                            {--ip= : 초기화할 IP 주소}
                            {--days=1 : X일 이전의 시도 초기화}
                            {--all : 모든 비밀번호 시도 초기화}';

    /**
     * 콘솔 명령어 설명
     * 
     * 비밀번호 실패로 차단된 사용자의 시도 횟수를 초기화하고
     * 차단을 해제합니다. 시스템 로그에 모든 작업이 기록됩니다.
     *
     * @var string
     */
    protected $description = '로그인 시도 횟수를 초기화하고 차단을 해제합니다';

    /**
     * 명령어 실행 메인 메서드
     * 
     * 전달된 옵션에 따라 적절한 초기화 방법을 선택하여 실행합니다.
     * 우선순위:
     * 1. --all : 모든 시도 초기화
     * 2. email : 특정 이메일 초기화
     * 3. --ip : 특정 IP 초기화
     * 4. --days : 오래된 시도 초기화
     * 
     * @return int 명령어 실행 결과 (0: 성공, 1: 실패)
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
     * 모든 차단된 시도 초기화
     * 
     * 현재 차단된 모든 레코드를 찾아 차단을 해제하고,
     * 각 이메일별로 초기화 로그를 생성합니다.
     * 
     * 작업 순서:
     * 1. 사용자 확인 요청
     * 2. 차단된 레코드 검색
     * 3. 차단 상태 해제
     * 4. 초기화 로그 생성
     * 5. 시스템 로그 기록
     * 
     * @return int 명령어 실행 결과
     */
    protected function resetAll()
    {
        if (! $this->confirm('모든 차단을 해제하시겠습니까?')) {
            $this->info('취소되었습니다.');

            return 0;
        }

        // 차단된 레코드만 찾기
        $blockedLogs = AdminPasswordLog::where('is_blocked', true)
            ->where('status', 'blocked')
            ->get();

        if ($blockedLogs->isEmpty()) {
            $this->info('차단된 기록이 없습니다.');

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
                'status' => 'unblocked',
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
                    'reset_all' => true,
                ],
            ]);
        }

        $this->info("총 {$count}개의 차단이 해제되었습니다.");

        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => 'ALL',
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => 'admin:password-reset --all',
        ]);

        return 0;
    }

    /**
     * 특정 이메일의 시도 초기화
     * 
     * 지정된 이메일에 대한 모든 차단된 레코드를 찾아
     * 차단을 해제하고 초기화 로그를 생성합니다.
     * 
     * @param string $email 초기화할 이메일 주소
     * @return int 명령어 실행 결과
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
                'status' => 'unblocked',
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
                'unblocked_count' => $count,
            ],
        ]);

        $this->info("✓ {$email}의 {$count}개 차단이 해제되었습니다.");

        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'email' => $email,
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => "admin:password-reset {$email}",
        ]);

        return 0;
    }

    /**
     * 특정 IP의 시도 초기화
     * 
     * 지정된 IP 주소에서 발생한 모든 차단된 레코드를 찾아
     * 차단을 해제합니다. 하나의 IP에서 여러 이메일이
     * 차단된 경우 모두 해제됩니다.
     * 
     * @param string $ip 초기화할 IP 주소
     * @return int 명령어 실행 결과
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
                'status' => 'unblocked',
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
                    'reset_ip' => $ip,
                ],
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
            'command' => "admin:password-reset --ip={$ip}",
        ]);

        return 0;
    }

    /**
     * 오래된 시도 초기화
     * 
     * 지정된 일수보다 오래된 차단된 레코드를 자동으로 해제합니다.
     * 기본값은 1일이며, --days 옵션으로 변경 가능합니다.
     * 
     * 주로 다음과 같은 경우 사용:
     * - 지원 팀의 확인 후 정당한 사용자 차단 해제
     * - 오래된 차단의 자동 정리
     * 
     * @return int 명령어 실행 결과
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

        if (! $this->confirm("{$days}일 이전의 {$blockedLogs->count()}개 차단을 해제하시겠습니까?")) {
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
                'status' => 'unblocked',
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
                    'days_old' => $days,
                ],
            ]);
        }

        $this->info("✓ {$days}일 이전의 {$count}개 차단이 해제되었습니다.");

        // 시스템 로그 기록
        AdminUserLog::log('password_attempts_reset', null, [
            'days_old' => $days,
            'reset_by' => 'console',
            'unblocked_count' => $count,
            'command' => "admin:password-reset --days={$days}",
        ]);

        return 0;
    }
}
