<?php

namespace Jiny\Admin\App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Jiny\Admin\App\Models\AdminUserLog;
use Illuminate\Support\Facades\DB;

class AdminPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:password-reset 
                            {email? : 관리자 이메일 주소}
                            {--password= : 새 비밀번호 (입력하지 않으면 프롬프트 표시)}
                            {--random : 랜덤 비밀번호 생성}
                            {--show : 생성된 비밀번호 표시}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '관리자 계정의 비밀번호를 재설정합니다';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 이메일 확인
        $email = $this->argument('email');
        
        if (!$email) {
            // 관리자 목록 표시
            $this->displayAdminList();
            $email = $this->ask('비밀번호를 재설정할 관리자의 이메일을 입력하세요');
        }
        
        // 사용자 확인
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("사용자를 찾을 수 없습니다: {$email}");
            return 1;
        }
        
        if (!$user->isAdmin) {
            $this->warn("경고: {$email}은(는) 관리자 계정이 아닙니다.");
            if (!$this->confirm('계속하시겠습니까?')) {
                return 0;
            }
        }
        
        // 사용자 정보 표시
        $this->displayUserInfo($user);
        
        // 비밀번호 설정
        $password = $this->getNewPassword();
        
        if (!$password) {
            $this->error('비밀번호 설정에 실패했습니다.');
            return 1;
        }
        
        // 비밀번호 업데이트
        $oldPasswordHash = $user->password;
        $user->password = Hash::make($password);
        $user->password_changed_at = now();
        $user->password_must_change = false;
        $user->force_password_change = false;
        
        // 비밀번호 만료일 설정 (설정에 따라)
        $expiryDays = config('admin.setting.password.expiry_days', 0);
        if ($expiryDays > 0) {
            $user->password_expires_at = now()->addDays($expiryDays);
            $user->password_expiry_days = $expiryDays;
        }
        
        $user->save();
        
        // 비밀번호 변경 로그 기록
        $this->logPasswordChange($user, $oldPasswordHash);
        
        // 성공 메시지
        $this->info("비밀번호가 성공적으로 재설정되었습니다.");
        $this->table(
            ['항목', '값'],
            [
                ['이메일', $user->email],
                ['이름', $user->name],
                ['관리자', $user->isAdmin ? 'Yes' : 'No'],
                ['타입', $user->utype ?? 'N/A'],
                ['비밀번호 변경일', now()->format('Y-m-d H:i:s')],
                ['비밀번호 만료일', $user->password_expires_at ? $user->password_expires_at->format('Y-m-d') : '없음'],
            ]
        );
        
        if ($this->option('show')) {
            $this->newLine();
            $this->warn("새 비밀번호: {$password}");
            $this->warn("보안을 위해 이 비밀번호를 안전한 곳에 기록한 후 화면을 지우세요.");
        }
        
        return 0;
    }
    
    /**
     * 관리자 목록 표시
     */
    private function displayAdminList()
    {
        $admins = User::where('isAdmin', true)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'email', 'name', 'utype', 'last_login_at']);
        
        if ($admins->isEmpty()) {
            $this->warn('등록된 관리자가 없습니다.');
            return;
        }
        
        $this->info('관리자 목록:');
        $this->table(
            ['ID', '이메일', '이름', '타입', '마지막 로그인'],
            $admins->map(function ($admin) {
                return [
                    $admin->id,
                    $admin->email,
                    $admin->name,
                    $admin->utype ?? 'N/A',
                    $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : 'Never',
                ];
            })
        );
        $this->newLine();
    }
    
    /**
     * 사용자 정보 표시
     */
    private function displayUserInfo($user)
    {
        $this->info('사용자 정보:');
        $this->table(
            ['항목', '값'],
            [
                ['ID', $user->id],
                ['이메일', $user->email],
                ['이름', $user->name],
                ['관리자', $user->isAdmin ? 'Yes' : 'No'],
                ['타입', $user->utype ?? 'N/A'],
                ['생성일', $user->created_at->format('Y-m-d H:i:s')],
                ['마지막 로그인', $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'],
                ['비밀번호 변경일', $user->password_changed_at ? (is_string($user->password_changed_at) ? $user->password_changed_at : $user->password_changed_at->format('Y-m-d H:i:s')) : 'Never'],
            ]
        );
        $this->newLine();
    }
    
    /**
     * 새 비밀번호 가져오기
     */
    private function getNewPassword()
    {
        // 랜덤 비밀번호 생성 옵션
        if ($this->option('random')) {
            return $this->generateRandomPassword();
        }
        
        // 옵션으로 제공된 비밀번호
        if ($this->option('password')) {
            $password = $this->option('password');
            if ($this->validatePassword($password)) {
                return $password;
            }
            $this->error('제공된 비밀번호가 보안 요구사항을 충족하지 않습니다.');
            return null;
        }
        
        // 대화식으로 비밀번호 입력
        $attempts = 0;
        while ($attempts < 3) {
            $password = $this->secret('새 비밀번호를 입력하세요');
            $passwordConfirm = $this->secret('비밀번호를 다시 입력하세요');
            
            if ($password !== $passwordConfirm) {
                $this->error('비밀번호가 일치하지 않습니다.');
                $attempts++;
                continue;
            }
            
            if ($this->validatePassword($password)) {
                return $password;
            }
            
            $attempts++;
            $this->error('비밀번호가 보안 요구사항을 충족하지 않습니다.');
            $this->displayPasswordRequirements();
        }
        
        return null;
    }
    
    /**
     * 비밀번호 유효성 검증
     */
    private function validatePassword($password)
    {
        $rules = [];
        $messages = [];
        
        // 최소 길이
        $minLength = config('admin.setting.password.min_length', 8);
        $rules[] = 'min:' . $minLength;
        
        // 최대 길이
        $maxLength = config('admin.setting.password.max_length', 128);
        $rules[] = 'max:' . $maxLength;
        
        // 정규식 규칙 생성
        $regex = '';
        if (config('admin.setting.password.require_uppercase', true)) {
            $regex .= '(?=.*[A-Z])';
            $messages['regex'] = '비밀번호는 대문자를 포함해야 합니다.';
        }
        if (config('admin.setting.password.require_lowercase', true)) {
            $regex .= '(?=.*[a-z])';
        }
        if (config('admin.setting.password.require_numbers', true)) {
            $regex .= '(?=.*[0-9])';
        }
        if (config('admin.setting.password.require_special_chars', true)) {
            $specialChars = preg_quote(config('admin.setting.password.allowed_special_chars', '!@#$%^&*()'), '/');
            $regex .= "(?=.*[{$specialChars}])";
        }
        
        if ($regex) {
            $rules[] = 'regex:/^' . $regex . '.*/';
        }
        
        $validator = Validator::make(
            ['password' => $password],
            ['password' => $rules],
            $messages
        );
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * 랜덤 비밀번호 생성
     */
    private function generateRandomPassword()
    {
        $length = config('admin.setting.password.generator.default_length', 16);
        $chars = '';
        
        if (config('admin.setting.password.generator.include_lowercase', true)) {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if (config('admin.setting.password.generator.include_uppercase', true)) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if (config('admin.setting.password.generator.include_numbers', true)) {
            $chars .= '0123456789';
        }
        if (config('admin.setting.password.generator.include_special', true)) {
            $chars .= config('admin.setting.password.allowed_special_chars', '!@#$%^&*()');
        }
        
        // 혼동하기 쉬운 문자 제외
        if (config('admin.setting.password.generator.exclude_ambiguous', true)) {
            $ambiguous = config('admin.setting.password.generator.ambiguous_chars', '0O1lI');
            $chars = str_replace(str_split($ambiguous), '', $chars);
        }
        
        $password = '';
        $charsLength = strlen($chars);
        
        // 각 유형별로 최소 1개씩 포함되도록 보장
        if (config('admin.setting.password.require_uppercase', true)) {
            $upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $password .= $upperChars[random_int(0, strlen($upperChars) - 1)];
        }
        if (config('admin.setting.password.require_lowercase', true)) {
            $lowerChars = 'abcdefghijklmnopqrstuvwxyz';
            $password .= $lowerChars[random_int(0, strlen($lowerChars) - 1)];
        }
        if (config('admin.setting.password.require_numbers', true)) {
            $numberChars = '0123456789';
            $password .= $numberChars[random_int(0, strlen($numberChars) - 1)];
        }
        if (config('admin.setting.password.require_special_chars', true)) {
            $specialChars = config('admin.setting.password.allowed_special_chars', '!@#$%^&*()');
            $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];
        }
        
        // 나머지 문자 채우기
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $chars[random_int(0, $charsLength - 1)];
        }
        
        // 문자 섞기
        $password = str_shuffle($password);
        
        $this->info("랜덤 비밀번호가 생성되었습니다.");
        
        return $password;
    }
    
    /**
     * 비밀번호 요구사항 표시
     */
    private function displayPasswordRequirements()
    {
        $this->info('비밀번호 요구사항:');
        $requirements = [];
        
        $requirements[] = ['최소 길이', config('admin.setting.password.min_length', 8) . '자'];
        $requirements[] = ['최대 길이', config('admin.setting.password.max_length', 128) . '자'];
        
        if (config('admin.setting.password.require_uppercase', true)) {
            $requirements[] = ['대문자', '필수'];
        }
        if (config('admin.setting.password.require_lowercase', true)) {
            $requirements[] = ['소문자', '필수'];
        }
        if (config('admin.setting.password.require_numbers', true)) {
            $requirements[] = ['숫자', '필수'];
        }
        if (config('admin.setting.password.require_special_chars', true)) {
            $requirements[] = ['특수문자', '필수 (' . config('admin.setting.password.allowed_special_chars', '!@#$%^&*()') . ')'];
        }
        
        $this->table(['요구사항', '값'], $requirements);
    }
    
    /**
     * 비밀번호 변경 로그 기록
     */
    private function logPasswordChange($user, $oldPasswordHash)
    {
        // AdminUserLog에 기록
        AdminUserLog::log('password_reset_console', $user, [
            'changed_by' => 'console',
            'command' => 'admin:password-reset',
            'executor' => get_current_user(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        // admin_user_password_logs 테이블에 직접 기록
        DB::table('admin_user_password_logs')->insert([
            'user_id' => $user->id,
            'old_password_hash' => $oldPasswordHash,
            'new_password_hash' => $user->password,
            'changed_by' => 'console:' . get_current_user(),
            'change_reason' => 'Password reset via console command',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}