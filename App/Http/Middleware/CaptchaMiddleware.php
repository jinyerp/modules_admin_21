<?php

namespace Jiny\Admin\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jiny\Admin\App\Services\Captcha\CaptchaManager;
use Jiny\Admin\App\Models\AdminUserLog;

class CaptchaMiddleware
{
    protected CaptchaManager $captchaManager;

    public function __construct(CaptchaManager $captchaManager)
    {
        $this->captchaManager = $captchaManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // CAPTCHA가 비활성화된 경우 통과
        if (!config('admin.setting.captcha.enabled')) {
            return $next($request);
        }

        // POST 요청이 아닌 경우 통과
        if (!$request->isMethod('post')) {
            return $next($request);
        }

        // CAPTCHA가 필요한지 확인
        $email = $request->input('email');
        $ip = $request->ip();
        
        if (!$this->captchaManager->isRequired($email, $ip)) {
            return $next($request);
        }

        // CAPTCHA 응답 확인
        $captchaResponse = $this->getCaptchaResponse($request);
        
        if (empty($captchaResponse)) {
            return $this->handleMissingCaptcha($request);
        }

        // CAPTCHA 검증
        try {
            $driver = $this->captchaManager->driver();
            
            if (!$driver->verify($captchaResponse, $ip)) {
                return $this->handleFailedCaptcha($request, $driver->getErrorMessage());
            }
            
            // CAPTCHA 성공 로그
            $this->logCaptchaSuccess($request, $driver->getScore());
            
        } catch (\Exception $e) {
            \Log::error('CAPTCHA middleware error: ' . $e->getMessage());
            
            // 엄격 모드에서는 에러 시 차단
            if (config('admin.setting.captcha.mode') === 'always') {
                return $this->handleCaptchaError($request);
            }
        }

        return $next($request);
    }

    /**
     * CAPTCHA 응답 가져오기
     *
     * @param Request $request
     * @return string|null
     */
    private function getCaptchaResponse(Request $request): ?string
    {
        // reCAPTCHA 응답 확인
        if ($request->has('g-recaptcha-response')) {
            return $request->input('g-recaptcha-response');
        }
        
        // hCaptcha 응답 확인
        if ($request->has('h-captcha-response')) {
            return $request->input('h-captcha-response');
        }
        
        return null;
    }

    /**
     * CAPTCHA 누락 처리
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleMissingCaptcha(Request $request)
    {
        // 로그 기록
        if (config('admin.setting.captcha.log.enabled')) {
            AdminUserLog::log('captcha_missing_middleware', null, [
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempt_time' => now()->toDateTimeString(),
            ]);
        }

        session()->flash('notification', [
            'type' => 'error',
            'title' => 'CAPTCHA 필요',
            'message' => config('admin.setting.captcha.messages.required'),
        ]);

        return redirect()->back()
            ->withErrors(['captcha' => config('admin.setting.captcha.messages.required')])
            ->withInput($request->except('password'));
    }

    /**
     * CAPTCHA 실패 처리
     *
     * @param Request $request
     * @param string|null $errorMessage
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleFailedCaptcha(Request $request, ?string $errorMessage = null)
    {
        // 실패 횟수 증가
        $this->captchaManager->incrementFailedAttempts(
            $request->input('email'),
            $request->ip()
        );

        // 로그 기록
        if (config('admin.setting.captcha.log.enabled')) {
            AdminUserLog::log('captcha_failed_middleware', null, [
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'error' => $errorMessage,
                'attempt_time' => now()->toDateTimeString(),
            ]);
        }

        session()->flash('notification', [
            'type' => 'error',
            'title' => 'CAPTCHA 실패',
            'message' => config('admin.setting.captcha.messages.failed'),
        ]);

        return redirect()->back()
            ->withErrors(['captcha' => config('admin.setting.captcha.messages.failed')])
            ->withInput($request->except('password'));
    }

    /**
     * CAPTCHA 에러 처리
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleCaptchaError(Request $request)
    {
        session()->flash('notification', [
            'type' => 'error',
            'title' => 'CAPTCHA 오류',
            'message' => config('admin.setting.captcha.messages.not_configured'),
        ]);

        return redirect()->back()
            ->withErrors(['captcha' => config('admin.setting.captcha.messages.not_configured')])
            ->withInput($request->except('password'));
    }

    /**
     * CAPTCHA 성공 로그
     *
     * @param Request $request
     * @param float|null $score
     * @return void
     */
    private function logCaptchaSuccess(Request $request, ?float $score = null)
    {
        if (config('admin.setting.captcha.log.enabled') && !config('admin.setting.captcha.log.failed_only')) {
            AdminUserLog::log('captcha_success_middleware', null, [
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'score' => $score,
                'attempt_time' => now()->toDateTimeString(),
            ]);
        }
    }
}