<?php

namespace Jiny\Admin\App\Services\Captcha;

use Illuminate\Support\Facades\Http;

class HcaptchaDriver implements CaptchaDriverInterface
{
    private string $siteKey;
    private string $secretKey;
    private ?string $lastError = null;
    private string $verifyUrl = 'https://hcaptcha.com/siteverify';

    public function __construct(array $config)
    {
        $this->siteKey = $config['site_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    public function render(array $options = []): string
    {
        $theme = $options['theme'] ?? 'light';
        $size = $options['size'] ?? 'normal';
        $tabindex = $options['tabindex'] ?? 0;
        
        return sprintf(
            '<div class="h-captcha" data-sitekey="%s" data-theme="%s" data-size="%s" data-tabindex="%s"></div>',
            $this->siteKey,
            $theme,
            $size,
            $tabindex
        );
    }

    public function getScript(): string
    {
        return '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
    }

    public function verify(string $response, ?string $remoteIp = null): bool
    {
        $data = [
            'secret' => $this->secretKey,
            'response' => $response,
        ];
        
        if ($remoteIp) {
            $data['remoteip'] = $remoteIp;
        }
        
        try {
            $response = Http::asForm()->post($this->verifyUrl, $data);
            
            if (!$response->successful()) {
                $this->lastError = 'hCaptcha 서버와 통신할 수 없습니다.';
                return false;
            }
            
            $result = $response->json();
            
            if (!isset($result['success']) || !$result['success']) {
                $errorCodes = $result['error-codes'] ?? ['unknown-error'];
                $this->lastError = $this->translateError($errorCodes[0]);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            $this->lastError = 'hCaptcha 검증 중 오류가 발생했습니다: ' . $e->getMessage();
            return false;
        }
    }

    public function getErrorMessage(): ?string
    {
        return $this->lastError;
    }

    public function getScore(): ?float
    {
        // hCaptcha는 점수 시스템을 사용하지 않음
        return null;
    }

    private function translateError(string $errorCode): string
    {
        $errors = [
            'missing-input-secret' => 'Secret 키가 누락되었습니다.',
            'invalid-input-secret' => 'Secret 키가 유효하지 않습니다.',
            'missing-input-response' => 'CAPTCHA 응답이 누락되었습니다.',
            'invalid-input-response' => 'CAPTCHA 응답이 유효하지 않습니다.',
            'bad-request' => '잘못된 요청입니다.',
            'invalid-or-already-seen-response' => 'CAPTCHA가 이미 사용되었습니다.',
            'not-using-dummy-passcode' => '테스트 모드에서는 더미 패스코드를 사용해야 합니다.',
            'sitekey-secret-mismatch' => 'Site key와 Secret key가 일치하지 않습니다.',
        ];
        
        return $errors[$errorCode] ?? 'CAPTCHA 검증에 실패했습니다.';
    }
}