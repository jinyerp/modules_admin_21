# SMS 관리 시스템

## 개요

@jiny/admin SMS 관리 시스템은 다양한 SMS 제공업체를 통해 문자 메시지를 발송하고 관리할 수 있는 통합 솔루션입니다.

## 주요 기능

### 1. SMS 제공업체 관리
- 여러 SMS 제공업체 등록 및 관리
- API 키와 인증 정보 안전하게 저장
- 기본 제공업체 설정
- 제공업체별 발송 통계 확인

### 2. SMS 발송
- 단일/대량 SMS 발송
- 발송 이력 관리
- 실시간 발송 상태 추적
- 발송 실패 시 재시도 기능

## 지원 제공업체

### Vonage (Nexmo)
현재 기본적으로 Vonage SMS API를 지원합니다.

#### 설정 방법
1. [Vonage Dashboard](https://dashboard.nexmo.com)에서 계정 생성
2. API Key와 API Secret 확인
3. 관리자 페이지에서 제공업체 등록

#### 환경 변수 설정
```env
VONAGE_API_KEY=your_api_key
VONAGE_API_SECRET=your_api_secret
VONAGE_SMS_FROM=your_sender_id
```

### 추가 예정 제공업체
- Twilio
- AWS SNS
- 국내 SMS 업체 (알리고, 솔루션링크 등)

## 사용 방법

### 제공업체 등록

1. 관리자 페이지 접속: `/admin/sms/provider`
2. "새 제공업체 추가" 클릭
3. 제공업체 정보 입력:
   - 제공업체명
   - API Key
   - API Secret
   - 발신번호
   - 활성화 여부
   - 기본 제공업체 설정

### SMS 발송

1. SMS 발송 페이지 접속: `/admin/sms/send`
2. "SMS 발송" 클릭
3. 발송 정보 입력:
   - 수신번호
   - 메시지 내용
   - 제공업체 선택 (옵션)

### 발송 이력 확인

- SMS 발송 페이지에서 발송된 모든 메시지 이력 확인
- 상태별 필터링 (대기중, 발송완료, 수신확인, 실패)
- 발송 시간, 비용, 제공업체 정보 확인

## 아키텍처

### 서비스 레이어
`SmsService` 클래스가 모든 SMS 관련 작업을 처리합니다.

```php
use Jiny\Admin\App\Services\SmsService;

$smsService = new SmsService();
$smsService->send($to, $message, $from);
```

### 데이터베이스 구조

#### admin_sms_providers 테이블
- SMS 제공업체 정보 저장
- API 인증 정보 암호화 저장
- 발송 통계 및 잔액 관리

#### admin_sms_sends 테이블
- 모든 SMS 발송 이력 저장
- 발송 상태 추적
- 응답 데이터 및 오류 정보 기록

### Hook 시스템

컨트롤러의 각 액션에 Hook을 추가하여 커스터마이징 가능:

```php
// Hook 예제: SMS 발송 전 검증
hookSmsBeforeSend($data) {
    // 발송 전 커스텀 검증 로직
    if (!$this->validatePhoneNumber($data['to_number'])) {
        throw new Exception('유효하지 않은 전화번호');
    }
}

// Hook 예제: SMS 발송 후 처리
hookSmsAfterSend($smsLog) {
    // 발송 후 추가 처리 (알림, 통계 업데이트 등)
    $this->notifyAdmins($smsLog);
}
```

## API 엔드포인트

### REST API (예정)
```
POST   /api/admin/sms/send       - SMS 발송
GET    /api/admin/sms/status/{id} - 발송 상태 확인
GET    /api/admin/sms/history    - 발송 이력 조회
```

## 보안

### API 키 관리
- API 키는 데이터베이스에 암호화되어 저장
- 환경 변수를 통한 민감 정보 관리
- 접근 권한 제어 (관리자만 접근 가능)

### 발송 제한
- Rate limiting 적용
- IP 기반 접근 제어
- 발송량 제한 설정 가능

## 문제 해결

### 일반적인 오류

#### 발송 실패
- API 키 확인
- 잔액 확인
- 발신번호 등록 여부 확인
- 네트워크 연결 상태 확인

#### 제공업체 연결 오류
- API 엔드포인트 확인
- 인증 정보 검증
- 제공업체 서비스 상태 확인

### 로그 확인
```bash
# Laravel 로그 확인
tail -f storage/logs/laravel.log

# SMS 발송 로그 확인
php artisan tinker
>>> \Jiny\Admin\App\Models\AdminSmsSend::latest()->take(10)->get();
```

## 개발 가이드

### 새 제공업체 추가

1. `SmsService`에 새 제공업체 메서드 추가:
```php
protected function sendViaTwilio($to, $message, $from)
{
    // Twilio API 구현
}
```

2. 제공업체 타입 추가:
```php
// AdminSmsProvider 모델
const PROVIDER_TWILIO = 'twilio';
```

3. 설정 뷰 업데이트로 새 제공업체 옵션 추가

### 커스텀 Hook 추가

`AdminSmsSend` 컨트롤러에 Hook 메서드 추가:

```php
public function hookIndexBefore($request)
{
    // 목록 조회 전 처리
}

public function hookStoreBefore(&$data)
{
    // 저장 전 데이터 검증/변환
}

public function hookStoreAfter($model)
{
    // 저장 후 추가 처리
}
```

## 성능 최적화

### 대량 발송
- 큐(Queue) 시스템 활용
- 배치 처리로 API 호출 최적화
- 비동기 발송 처리

### 캐싱
- 제공업체 정보 캐싱
- 자주 사용되는 템플릿 캐싱

## 로드맵

### 단기 계획
- [ ] Twilio 지원 추가
- [ ] SMS 템플릿 기능
- [ ] 예약 발송 기능
- [ ] 대량 발송 UI 개선

### 장기 계획
- [ ] 국제 SMS 지원
- [ ] MMS 지원
- [ ] 발송 통계 대시보드
- [ ] Webhook 지원
- [ ] 발송 실패 자동 재시도

## 라이선스

이 시스템은 @jiny/admin 패키지의 일부로 제공됩니다.

## 지원

문제가 발생하거나 도움이 필요한 경우:
- GitHub Issues에 문의
- 관리자 문서 참조
- 커뮤니티 포럼 활용