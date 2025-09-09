<?php

namespace Jiny\Admin\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Jiny\Admin\Mail\EmailMailable;
use Exception;

/**
 * 통합 알림 서비스
 * 
 * 이벤트 기반 이메일 알림을 관리하고 발송합니다.
 */
class NotificationService
{
    protected $templateService;
    protected $logService;
    protected $hooks = [];

    public function __construct()
    {
        $this->templateService = new EmailTemplateService();
        $this->logService = new EmailLogService();
    }

    /**
     * Hook 등록
     */
    public function registerHook(string $event, callable $callback): void
    {
        if (!isset($this->hooks[$event])) {
            $this->hooks[$event] = [];
        }
        $this->hooks[$event][] = $callback;
    }

    /**
     * 이벤트 기반 알림 발송
     */
    public function notify(string $eventType, array $data = []): bool
    {
        try {
            // 알림 규칙 조회
            $rules = $this->getActiveRules($eventType);

            if ($rules->isEmpty()) {
                Log::info("No active notification rules for event: {$eventType}");
                return false;
            }

            $success = true;

            foreach ($rules as $rule) {
                // 조건 체크
                if (!$this->checkConditions($rule, $data)) {
                    continue;
                }

                // 스로틀링 체크
                if (!$this->checkThrottle($rule, $data)) {
                    Log::info("Notification throttled for rule: {$rule->name}");
                    continue;
                }

                // 수신자 결정
                $recipients = $this->determineRecipients($rule, $data);

                foreach ($recipients as $recipient) {
                    // 발송 전 Hook 실행
                    if (!$this->executeHooks('before_send', $eventType, $data, $recipient)) {
                        continue;
                    }

                    // 이메일 발송
                    $result = $this->sendNotification($rule, $recipient, $data);

                    if (!$result) {
                        $success = false;
                    }

                    // 발송 후 Hook 실행
                    $this->executeHooks('after_send', $eventType, $data, $recipient, $result);
                }

                // 규칙 통계 업데이트
                $this->updateRuleStatistics($rule->id);
            }

            return $success;

        } catch (Exception $e) {
            Log::error("Notification failed for event {$eventType}: " . $e->getMessage(), [
                'event' => $eventType,
                'data' => $data,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * 특정 이벤트 알림 메서드들
     */
    
    /**
     * 로그인 실패 알림
     */
    public function notifyLoginFailed(string $email, int $failedAttempts, string $ipAddress = null): bool
    {
        return $this->notify('login_failed', [
            'user_email' => $email,
            'failed_attempts' => $failedAttempts,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempted_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * 2FA 설정 변경 알림
     */
    public function notify2FAChanged(int $userId, string $action = 'enabled'): bool
    {
        $user = DB::table('users')->where('id', $userId)->first();
        
        if (!$user) {
            return false;
        }

        return $this->notify('two_fa_' . $action, [
            'user_id' => $userId,
            'user_name' => $user->name,
            'user_email' => $user->email,
            $action . '_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * IP 차단 알림
     */
    public function notifyIPBlocked(string $ipAddress, string $reason, int $blockedMinutes = 60): bool
    {
        return $this->notify('ip_blocked', [
            'ip_address' => $ipAddress,
            'blocked_reason' => $reason,
            'blocked_at' => now()->format('Y-m-d H:i:s'),
            'blocked_until' => now()->addMinutes($blockedMinutes)->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * 비밀번호 변경 알림
     */
    public function notifyPasswordChanged(int $userId): bool
    {
        $user = DB::table('users')->where('id', $userId)->first();
        
        if (!$user) {
            return false;
        }

        return $this->notify('password_changed', [
            'user_id' => $userId,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'changed_at' => now()->format('Y-m-d H:i:s'),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * 계정 잠금 알림
     */
    public function notifyAccountLocked(int $userId, string $reason = null): bool
    {
        $user = DB::table('users')->where('id', $userId)->first();
        
        if (!$user) {
            return false;
        }

        return $this->notify('account_locked', [
            'user_id' => $userId,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'locked_reason' => $reason ?? '반복된 로그인 실패',
            'locked_at' => now()->format('Y-m-d H:i:s'),
            'unlock_link' => url('/unlock/' . encrypt($user->email))
        ]);
    }

    /**
     * 활성 알림 규칙 조회
     */
    protected function getActiveRules(string $eventType)
    {
        $now = now();
        $currentTime = $now->format('H:i:s');
        $currentDay = $now->dayOfWeek;

        return DB::table('admin_email_notification_rules')
            ->where('event_type', $eventType)
            ->where('is_active', true)
            ->where(function ($query) use ($currentTime) {
                $query->whereNull('active_from')
                    ->orWhere('active_from', '<=', $currentTime);
            })
            ->where(function ($query) use ($currentTime) {
                $query->whereNull('active_to')
                    ->orWhere('active_to', '>=', $currentTime);
            })
            ->get()
            ->filter(function ($rule) use ($currentDay) {
                // 활성 요일 체크
                if ($rule->active_days) {
                    $activeDays = json_decode($rule->active_days, true);
                    if (!in_array($currentDay, $activeDays)) {
                        return false;
                    }
                }
                return true;
            });
    }

    /**
     * 조건 체크
     */
    protected function checkConditions($rule, array $data): bool
    {
        if (!$rule->conditions) {
            return true;
        }

        $conditions = json_decode($rule->conditions, true);

        foreach ($conditions as $field => $condition) {
            if (!isset($data[$field])) {
                return false;
            }

            // 조건 평가 (예: failed_attempts > 3)
            if (is_array($condition)) {
                $operator = $condition['operator'] ?? '=';
                $value = $condition['value'] ?? null;

                if (!$this->evaluateCondition($data[$field], $operator, $value)) {
                    return false;
                }
            } elseif ($data[$field] != $condition) {
                return false;
            }
        }

        return true;
    }

    /**
     * 조건 평가
     */
    protected function evaluateCondition($fieldValue, string $operator, $value): bool
    {
        switch ($operator) {
            case '>':
                return $fieldValue > $value;
            case '>=':
                return $fieldValue >= $value;
            case '<':
                return $fieldValue < $value;
            case '<=':
                return $fieldValue <= $value;
            case '!=':
                return $fieldValue != $value;
            case 'in':
                return in_array($fieldValue, (array) $value);
            case 'not_in':
                return !in_array($fieldValue, (array) $value);
            case '=':
            default:
                return $fieldValue == $value;
        }
    }

    /**
     * 스로틀링 체크
     */
    protected function checkThrottle($rule, array $data): bool
    {
        if (!$rule->throttle_minutes) {
            return true;
        }

        // 최근 발송 체크
        $lastSent = DB::table('admin_email_logs')
            ->where('event_type', $rule->event_type)
            ->where('status', 'sent')
            ->where('created_at', '>', now()->subMinutes($rule->throttle_minutes))
            ->exists();

        return !$lastSent;
    }

    /**
     * 수신자 결정
     */
    protected function determineRecipients($rule, array $data): array
    {
        $recipients = [];

        switch ($rule->recipient_type) {
            case 'user':
                // 이벤트 관련 사용자
                if (isset($data['user_email'])) {
                    $recipients[] = [
                        'email' => $data['user_email'],
                        'name' => $data['user_name'] ?? null
                    ];
                } elseif (isset($data['user_id'])) {
                    $user = DB::table('users')->where('id', $data['user_id'])->first();
                    if ($user) {
                        $recipients[] = [
                            'email' => $user->email,
                            'name' => $user->name
                        ];
                    }
                }
                break;

            case 'admin':
                // 모든 관리자
                $admins = DB::table('users')
                    ->where('is_admin', true)
                    ->get();
                foreach ($admins as $admin) {
                    $recipients[] = [
                        'email' => $admin->email,
                        'name' => $admin->name
                    ];
                }
                break;

            case 'role':
                // 특정 역할의 사용자들
                if ($rule->recipients) {
                    $roles = json_decode($rule->recipients, true);
                    $users = DB::table('users')
                        ->whereIn('role', $roles)
                        ->get();
                    foreach ($users as $user) {
                        $recipients[] = [
                            'email' => $user->email,
                            'name' => $user->name
                        ];
                    }
                }
                break;

            case 'custom':
                // 지정된 이메일 주소들
                if ($rule->recipients) {
                    $emails = json_decode($rule->recipients, true);
                    foreach ($emails as $email) {
                        $recipients[] = [
                            'email' => $email,
                            'name' => null
                        ];
                    }
                }
                break;
        }

        // 추가 수신자 설정
        if ($rule->notify_admins) {
            $admins = DB::table('users')
                ->where('is_admin', true)
                ->get();
            foreach ($admins as $admin) {
                $recipients[] = [
                    'email' => $admin->email,
                    'name' => $admin->name
                ];
            }
        }

        // 중복 제거
        $uniqueRecipients = [];
        $emails = [];
        foreach ($recipients as $recipient) {
            if (!in_array($recipient['email'], $emails)) {
                $uniqueRecipients[] = $recipient;
                $emails[] = $recipient['email'];
            }
        }

        return $uniqueRecipients;
    }

    /**
     * 알림 발송
     */
    protected function sendNotification($rule, array $recipient, array $data): bool
    {
        try {
            // 템플릿 가져오기
            $template = null;
            if ($rule->template_id) {
                $template = $this->templateService->getTemplateById($rule->template_id);
            } elseif ($rule->template_slug) {
                $template = $this->templateService->getTemplate($rule->template_slug);
            }

            if (!$template) {
                Log::error("No template found for rule: {$rule->name}");
                return false;
            }

            // 템플릿 렌더링
            $rendered = $this->templateService->render($template, $data);

            // 로그 생성
            $logId = $this->logService->createLog([
                'template_id' => $template->id,
                'template_slug' => $template->slug,
                'to_email' => $recipient['email'],
                'to_name' => $recipient['name'],
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'subject' => $rendered['subject'],
                'body' => $rendered['body'],
                'variables' => $data,
                'event_type' => $rule->event_type,
                'priority' => $rule->priority,
                'user_id' => $data['user_id'] ?? null
            ]);

            // 지연 발송 처리
            if ($rule->delay_seconds > 0) {
                // 큐에 지연 추가 (Laravel Queue 사용 시)
                // 여기서는 즉시 발송으로 구현
                sleep(min($rule->delay_seconds, 5)); // 최대 5초 지연
            }

            // 메일 발송
            Mail::to($recipient['email'])->send(new EmailMailable(
                $rendered['subject'],
                $rendered['body'],
                config('mail.from.address'),
                config('mail.from.name'),
                $recipient['email']
            ));

            // 발송 성공 기록
            $this->logService->markAsSent($logId);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage(), [
                'rule' => $rule->name,
                'recipient' => $recipient,
                'data' => $data,
                'exception' => $e
            ]);

            // 발송 실패 기록
            if (isset($logId)) {
                $this->logService->markAsFailed($logId, $e->getMessage());
            }

            return false;
        }
    }

    /**
     * Hook 실행
     */
    protected function executeHooks(string $timing, string $eventType, array $data, array $recipient, bool $result = null)
    {
        $hookKey = "{$timing}_{$eventType}";
        
        if (isset($this->hooks[$hookKey])) {
            foreach ($this->hooks[$hookKey] as $hook) {
                try {
                    $continue = call_user_func($hook, $data, $recipient, $result);
                    if ($continue === false) {
                        return false;
                    }
                } catch (Exception $e) {
                    Log::error("Hook execution failed: " . $e->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * 규칙 통계 업데이트
     */
    protected function updateRuleStatistics(int $ruleId): void
    {
        DB::table('admin_email_notification_rules')
            ->where('id', $ruleId)
            ->update([
                'sent_count' => DB::raw('sent_count + 1'),
                'last_sent_at' => now(),
                'updated_at' => now()
            ]);
    }

    /**
     * 알림 규칙 생성
     */
    public function createRule(array $data): int
    {
        return DB::table('admin_email_notification_rules')->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'event_type' => $data['event_type'],
            'conditions' => isset($data['conditions']) ? json_encode($data['conditions']) : null,
            'recipient_type' => $data['recipient_type'] ?? 'user',
            'recipients' => isset($data['recipients']) ? json_encode($data['recipients']) : null,
            'notify_user' => $data['notify_user'] ?? true,
            'notify_admins' => $data['notify_admins'] ?? false,
            'template_id' => $data['template_id'] ?? null,
            'template_slug' => $data['template_slug'] ?? null,
            'throttle_minutes' => $data['throttle_minutes'] ?? null,
            'max_per_day' => $data['max_per_day'] ?? null,
            'max_per_hour' => $data['max_per_hour'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
            'delay_seconds' => $data['delay_seconds'] ?? 0,
            'active_from' => $data['active_from'] ?? null,
            'active_to' => $data['active_to'] ?? null,
            'active_days' => isset($data['active_days']) ? json_encode($data['active_days']) : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}