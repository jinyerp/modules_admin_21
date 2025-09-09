<?php

namespace Jiny\Admin\App\Services;

use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Jiny\Admin\App\Models\AdminSmsProvider;
use Jiny\Admin\App\Models\AdminSmsSend;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    protected $client;
    protected $provider;
    
    /**
     * SMS 서비스 생성자
     */
    public function __construct()
    {
        $this->loadDefaultProvider();
    }
    
    /**
     * 기본 SMS 제공업체 로드
     */
    protected function loadDefaultProvider()
    {
        $this->provider = AdminSmsProvider::where('is_active', true)
            ->where('is_default', true)
            ->first();
            
        if (!$this->provider) {
            $this->provider = AdminSmsProvider::where('is_active', true)
                ->orderBy('priority', 'desc')
                ->first();
        }
        
        if ($this->provider) {
            $this->initializeClient();
        }
    }
    
    /**
     * 특정 제공업체로 설정
     */
    public function setProvider($providerId)
    {
        $this->provider = AdminSmsProvider::find($providerId);
        
        if ($this->provider && $this->provider->is_active) {
            $this->initializeClient();
            return true;
        }
        
        return false;
    }
    
    /**
     * SMS 클라이언트 초기화
     */
    protected function initializeClient()
    {
        if (!$this->provider) {
            return;
        }
        
        switch (strtolower($this->provider->provider_name)) {
            case 'vonage':
            case 'nexmo':
                $basic = new Basic($this->provider->api_key, $this->provider->api_secret);
                $this->client = new Client($basic);
                break;
                
            // 다른 제공업체 추가 가능
            default:
                throw new Exception("Unsupported SMS provider: {$this->provider->provider_name}");
        }
    }
    
    /**
     * SMS 발송
     */
    public function send($to, $message, $from = null)
    {
        if (!$this->provider || !$this->client) {
            throw new Exception('SMS provider not configured');
        }
        
        // 발송 이력 레코드 생성
        $smsLog = AdminSmsSend::create([
            'provider_id' => $this->provider->id,
            'provider_name' => $this->provider->provider_name,
            'to_number' => $to,
            'from_number' => $from ?? $this->provider->from_number,
            'from_name' => $this->provider->from_name,
            'message' => $message,
            'message_length' => mb_strlen($message),
            'message_count' => $this->calculateMessageCount($message),
            'status' => 'pending',
            'sent_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        try {
            $response = $this->sendViaProvider($to, $message, $from);
            
            // 성공 시 업데이트
            $smsLog->update([
                'status' => 'sent',
                'message_id' => $response['message_id'] ?? null,
                'cost' => $response['cost'] ?? null,
                'currency' => $response['currency'] ?? null,
                'response' => $response,
                'sent_at' => now(),
            ]);
            
            // 제공업체 통계 업데이트
            $this->provider->increment('sent_count');
            $this->provider->update(['last_used_at' => now()]);
            
            return [
                'success' => true,
                'message_id' => $response['message_id'] ?? null,
                'log_id' => $smsLog->id,
                'response' => $response,
            ];
            
        } catch (Exception $e) {
            // 실패 시 업데이트
            $smsLog->update([
                'status' => 'failed',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'failed_at' => now(),
            ]);
            
            Log::error('SMS sending failed', [
                'provider' => $this->provider->provider_name,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * 제공업체별 발송 처리
     */
    protected function sendViaProvider($to, $message, $from = null)
    {
        // provider_type 또는 provider_name으로 체크
        $providerType = strtolower($this->provider->provider_type ?? '');
        $providerName = strtolower($this->provider->provider_name ?? '');
        
        // Vonage/Nexmo 체크
        if ($providerType === 'vonage' || 
            $providerType === 'nexmo' || 
            strpos($providerName, 'vonage') !== false || 
            strpos($providerName, 'nexmo') !== false) {
            return $this->sendViaVonage($to, $message, $from);
        }
        
        // 다른 제공업체 추가 가능
        switch ($providerType) {
            case 'twilio':
                throw new Exception("Twilio provider not yet implemented");
                
            case 'aws_sns':
                throw new Exception("AWS SNS provider not yet implemented");
                
            default:
                throw new Exception("Unsupported SMS provider: {$this->provider->provider_name} (type: {$providerType})");
        }
    }
    
    /**
     * Vonage를 통한 SMS 발송
     */
    protected function sendViaVonage($to, $message, $from = null)
    {
        $from = $from ?? $this->provider->from_number ?? $this->provider->from_name ?? 'BRAND_NAME';
        
        // 한국 번호 형식 정리 (국가코드 추가)
        if (preg_match('/^0\d{9,10}$/', $to)) {
            $to = '82' . substr($to, 1);
        }
        
        $sms = new SMS($to, $from, $message);
        $response = $this->client->sms()->send($sms);
        
        $current = $response->current();
        
        if ($current->getStatus() == 0) {
            return [
                'message_id' => $current->getMessageId(),
                'status' => $current->getStatus(),
                'to' => $current->getTo(),
                'cost' => $current->getMessagePrice(),
                'currency' => 'USD',
                'network' => $current->getNetwork(),
                'remaining_balance' => $current->getRemainingBalance(),
            ];
        } else {
            throw new Exception("SMS failed with status: " . $current->getStatus());
        }
    }
    
    /**
     * 메시지 분할 수 계산
     */
    protected function calculateMessageCount($message)
    {
        $length = mb_strlen($message);
        
        // SMS 표준: 한글 70자, 영문 160자
        // 간단하게 한글 기준으로 계산
        if ($length <= 70) {
            return 1;
        } elseif ($length <= 134) {
            return 2;
        } elseif ($length <= 201) {
            return 3;
        } else {
            return ceil($length / 67);
        }
    }
    
    /**
     * 발송 이력 조회
     */
    public function getHistory($filters = [])
    {
        $query = AdminSmsSend::query();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }
        
        if (isset($filters['to_number'])) {
            $query->where('to_number', 'like', '%' . $filters['to_number'] . '%');
        }
        
        if (isset($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        
        if (isset($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
    
    /**
     * 제공업체 목록 조회
     */
    public function getProviders($activeOnly = false)
    {
        $query = AdminSmsProvider::query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('priority', 'desc')->get();
    }
    
    /**
     * 잔액 조회 (Vonage)
     */
    public function getBalance()
    {
        if (!$this->provider || !$this->client) {
            return null;
        }
        
        try {
            if (in_array(strtolower($this->provider->provider_name), ['vonage', 'nexmo'])) {
                $balance = $this->client->account()->getBalance();
                
                $this->provider->update([
                    'balance' => $balance->getBalance(),
                ]);
                
                return $balance->getBalance();
            }
        } catch (Exception $e) {
            Log::error('Failed to get SMS balance', [
                'provider' => $this->provider->provider_name,
                'error' => $e->getMessage(),
            ]);
        }
        
        return null;
    }
}