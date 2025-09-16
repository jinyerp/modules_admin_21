# Admin Database Schema Documentation

## 개요

이 문서는 Jiny Admin 패키지의 데이터베이스 스키마를 설명합니다. 모든 테이블은 관리자 시스템의 보안, 인증, 알림, 로깅 등을 관리하기 위해 설계되었습니다.

## 현재 구현된 테이블 구조

### 1. 사용자 관리 (우선순위 1-2)

#### users 테이블 확장
Laravel 기본 users 테이블에 관리자 시스템에 필요한 추가 컬럼들을 확장합니다.
2FA 설정, 계정 상태, 로그인 시도, 비밀번호 정책, 아바타 등 관리자 관련 필드를 포함하며, 
기존 사용자 시스템과 완벽하게 통합되어 관리자 권한을 세밀하게 제어할 수 있습니다.

**추가 컬럼:**
- `is_admin` (boolean) - 관리자 여부
- `admin_type_id` (unsignedBigInteger) - 관리자 타입 ID (admin_user_types 참조)
- `two_factor_secret` (text) - TOTP 시크릿 키
- `two_factor_recovery_codes` (text) - 2FA 복구 코드
- `two_factor_confirmed_at` (timestamp) - 2FA 활성화 시간
- `two_factor_enabled` (boolean) - 2FA 사용 여부
- `two_factor_method` (string) - 2FA 방법 (totp, sms, email)
- `two_factor_phone` (string) - 2FA SMS용 전화번호
- `two_factor_email` (string) - 2FA 이메일
- `account_status` (string) - 계정 상태 (active, suspended, locked, pending)
- `account_locked_at` (timestamp) - 계정 잠금 시간
- `account_locked_reason` (string) - 계정 잠금 사유
- `failed_login_attempts` (integer) - 실패한 로그인 시도 횟수
- `last_failed_login_at` (timestamp) - 마지막 로그인 실패 시간
- `last_login_at` (timestamp) - 마지막 로그인 시간
- `last_login_ip` (string) - 마지막 로그인 IP
- `login_count` (integer) - 총 로그인 횟수
- `last_activity_at` (timestamp) - 마지막 활동 시간
- `password_changed_at` (timestamp) - 비밀번호 변경 시간
- `password_expires_at` (timestamp) - 비밀번호 만료 시간
- `must_change_password` (boolean) - 다음 로그인 시 비밀번호 변경 필요
- `avatar` (string) - 아바타 이미지 경로

#### admin_user_types
관리자 권한 레벨과 역할을 정의하는 테이블로, super admin, admin, staff 등의 계층형 권한 구조를 관리합니다.
각 타입별로 세부 권한(permissions)과 설정(settings)을 JSON 형식으로 저장하여 유연한 권한 관리가 가능하며,
배지 색상, 정렬 순서, 활성화 상태 등 UI 표시와 관련된 정보도 함께 관리합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `name` (string) - 타입명 (super_admin, admin, staff 등)
- `display_name` (string) - 표시명
- `description` (text) - 설명
- `level` (integer) - 권한 레벨 (높을수록 강한 권한)
- `permissions` (json) - 세부 권한 목록
- `settings` (json) - 타입별 설정
- `badge_color` (string) - UI 배지 색상
- `sort_order` (integer) - 정렬 순서
- `is_active` (boolean) - 활성화 상태
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_user_logs
사용자의 모든 활동을 추적하는 감사 로그 테이블로, 로그인/로그아웃, 권한 변경, 데이터 수정 등을 기록합니다.
IP 주소, User Agent, 2FA 사용 여부 등 보안 관련 정보를 함께 저장하여 이상 행동 감지와 보안 감사에 활용되며,
extra_data 필드를 통해 이벤트별 추가 정보를 유연하게 저장할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID
- `target_user_id` (unsignedBigInteger) - 대상 사용자 ID (다른 사용자에 대한 작업 시)
- `target_type` (string) - 대상 타입 (user, role, permission 등)
- `target_id` (unsignedBigInteger) - 대상 ID
- `action` (string) - 액션 (login, logout, create, update, delete 등)
- `description` (text) - 상세 설명
- `ip_address` (string) - IP 주소
- `user_agent` (string) - 브라우저/디바이스 정보
- `two_factor_used` (boolean) - 2FA 사용 여부
- `extra_data` (json) - 추가 데이터
- `data` (json) - 변경 데이터 (이전/이후 값)
- `created_at` (timestamp) - 로그 생성 시간

#### admin_password_logs
비밀번호 관련 모든 이벤트를 기록하는 테이블로, 변경 시도, 성공/실패, 재설정 요청 등을 추적합니다.
브루트포스 공격 방지를 위한 시도 횟수 제한과 일시 차단 기능을 제공하며,
IP 주소와 User Agent를 기록하여 의심스러운 비밀번호 변경 시도를 모니터링할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID
- `action` (string) - 액션 (change, reset_request, reset_complete, failed_attempt)
- `status` (string) - 상태 (success, failed, pending)
- `ip_address` (string) - IP 주소
- `user_agent` (string) - 브라우저 정보
- `attempt_count` (integer) - 시도 횟수
- `blocked_until` (timestamp) - 차단 종료 시간
- `reason` (string) - 사유
- `metadata` (json) - 추가 메타데이터
- `created_at` (timestamp) - 생성 시간

#### admin_user_sessions
활성 세션을 관리하는 테이블로, 동시 로그인 제한과 세션 모니터링 기능을 제공합니다.
각 세션의 IP 주소, User Agent, 마지막 활동 시간을 추적하여 비정상적인 접근을 감지하고,
관리자가 특정 세션을 강제 종료하거나 모든 세션을 일괄 종료할 수 있는 기능을 지원합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID
- `session_id` (string) - 세션 ID
- `ip_address` (string) - IP 주소
- `user_agent` (string) - 브라우저 정보
- `location` (string) - 위치 정보
- `device_type` (string) - 디바이스 타입 (desktop, mobile, tablet)
- `browser` (string) - 브라우저 종류
- `platform` (string) - 운영체제
- `last_activity` (timestamp) - 마지막 활동 시간
- `login_at` (timestamp) - 로그인 시간
- `logout_at` (timestamp) - 로그아웃 시간
- `is_active` (boolean) - 활성 상태
- `terminated_by` (unsignedBigInteger) - 종료한 관리자 ID
- `termination_reason` (string) - 종료 사유
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

### 2. 보안 기능 (우선순위 3-7)

#### admin_2fa_codes
SMS나 이메일로 발송되는 2FA 인증 코드를 임시 저장하는 테이블입니다.
6자리 숫자 코드와 만료 시간(5분), 시도 횟수를 관리하여 보안을 강화하며,
TOTP 방식과 함께 사용되어 다양한 2FA 옵션을 사용자에게 제공합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID
- `code` (string) - 6자리 인증 코드
- `type` (string) - 타입 (sms, email)
- `verified` (boolean) - 검증 여부
- `attempts` (integer) - 시도 횟수
- `expires_at` (timestamp) - 만료 시간
- `verified_at` (timestamp) - 검증 시간
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_unlock_tokens
계정 잠금 해제를 위한 일회용 토큰을 관리하는 테이블입니다.
로그인 실패나 비정상 접근으로 잠긴 계정을 안전하게 해제할 수 있는 링크를 이메일로 발송하며,
토큰은 SHA256으로 해시되어 저장되고 60분 후 자동 만료되어 보안을 보장합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID
- `token` (string) - 해시된 토큰 (SHA256)
- `email` (string) - 이메일 주소
- `ip_address` (string) - 요청 IP 주소
- `user_agent` (string) - 브라우저 정보
- `used` (boolean) - 사용 여부
- `used_at` (timestamp) - 사용 시간
- `expires_at` (timestamp) - 만료 시간
- `created_at` (timestamp) - 생성 시간

#### admin_ip_attempts
IP 주소별 로그인 시도를 추적하여 브루트포스 공격을 방지하는 테이블입니다.
설정된 횟수 이상 실패 시 자동으로 IP를 일시 차단하며,
성공/실패 기록과 메타데이터를 저장하여 공격 패턴을 분석할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `ip_address` (string) - IP 주소
- `user_id` (unsignedBigInteger) - 시도한 사용자 ID
- `action` (string) - 액션 (login, register, password_reset)
- `success` (boolean) - 성공 여부
- `attempts` (integer) - 시도 횟수
- `last_attempt_at` (timestamp) - 마지막 시도 시간
- `blocked_until` (timestamp) - 차단 종료 시간
- `user_agent` (string) - 브라우저 정보
- `metadata` (json) - 추가 메타데이터
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_ip_blacklist
영구적 또는 임시적으로 차단할 IP 주소를 관리하는 테이블입니다.
단일 IP뿐만 아니라 CIDR 표기법을 통한 IP 대역 차단도 지원하며,
차단 사유와 만료 시간을 설정하여 유연한 접근 제어가 가능합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `ip_address` (string) - IP 주소 또는 CIDR
- `reason` (text) - 차단 사유
- `type` (string) - 차단 타입 (permanent, temporary)
- `expires_at` (timestamp) - 만료 시간 (임시 차단)
- `blocked_by` (unsignedBigInteger) - 차단한 관리자 ID
- `auto_blocked` (boolean) - 자동 차단 여부
- `attempt_count` (integer) - 차단 전 시도 횟수
- `last_attempt_at` (timestamp) - 마지막 접근 시도 시간
- `notes` (text) - 관리자 메모
- `is_active` (boolean) - 활성화 상태
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_ip_whitelist
신뢰할 수 있는 IP 주소를 관리하여 보안 정책에서 예외 처리하는 테이블입니다.
본사 사무실, VPN 서버 등 안전한 IP를 등록하여 추가 인증 없이 접근을 허용하며,
임시 허용 기능과 접근 통계를 제공하여 화이트리스트를 효율적으로 관리할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `ip_address` (string) - IP 주소 또는 CIDR
- `name` (string) - 이름/설명
- `type` (string) - 타입 (office, vpn, home, temporary)
- `expires_at` (timestamp) - 만료 시간 (임시 허용)
- `added_by` (unsignedBigInteger) - 추가한 관리자 ID
- `bypass_2fa` (boolean) - 2FA 우회 허용
- `bypass_captcha` (boolean) - CAPTCHA 우회 허용
- `access_count` (integer) - 접근 횟수
- `last_access_at` (timestamp) - 마지막 접근 시간
- `notes` (text) - 관리자 메모
- `is_active` (boolean) - 활성화 상태
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_captcha_logs
CAPTCHA 검증 결과를 기록하여 봇 공격과 자동화된 접근을 모니터링하는 테이블입니다.
reCAPTCHA, hCaptcha 등 다양한 제공자를 지원하며 검증 점수와 성공/실패를 기록하고,
액션별(로그인, 회원가입 등) 통계를 제공하여 CAPTCHA 정책을 최적화할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `user_id` (unsignedBigInteger) - 사용자 ID (비회원일 경우 null)
- `action` (string) - 액션 (login, register, password_reset, contact)
- `provider` (string) - 제공자 (recaptcha, hcaptcha, turnstile)
- `success` (boolean) - 검증 성공 여부
- `score` (decimal) - 검증 점수 (0.0 ~ 1.0)
- `challenge_ts` (timestamp) - 챌린지 타임스탬프
- `hostname` (string) - 호스트명
- `error_codes` (json) - 에러 코드 목록
- `ip_address` (string) - IP 주소
- `user_agent` (string) - 브라우저 정보
- `response_time` (integer) - 응답 시간 (밀리초)
- `metadata` (json) - 추가 메타데이터
- `created_at` (timestamp) - 생성 시간

### 3. 알림 시스템 (우선순위 8-13)

#### admin_webhook_configs
외부 시스템과 연동하기 위한 Webhook 설정을 관리하는 테이블입니다.
이벤트별로 호출할 URL과 헤더, 재시도 정책을 설정할 수 있으며,
서명 검증을 통한 보안 기능과 이벤트 필터링으로 필요한 알림만 전송할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `name` (string) - Webhook 이름
- `url` (string) - 대상 URL
- `method` (string) - HTTP 메소드 (POST, GET)
- `events` (json) - 구독 이벤트 목록
- `headers` (json) - 사용자 정의 헤더
- `secret` (string) - 서명 검증용 시크릿
- `timeout` (integer) - 타임아웃 (초)
- `retry_times` (integer) - 재시도 횟수
- `retry_delay` (integer) - 재시도 지연 시간 (초)
- `active` (boolean) - 활성화 상태
- `last_triggered_at` (timestamp) - 마지막 호출 시간
- `success_count` (integer) - 성공 횟수
- `failure_count` (integer) - 실패 횟수
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_push_providers
FCM, APNs, Web Push 등 푸시 알림 제공자 설정을 관리하는 테이블입니다.
각 제공자별 API 키와 인증서를 안전하게 저장하며,
기본 제공자 설정과 우선순위 관리로 안정적인 푸시 알림 서비스를 제공합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `name` (string) - 제공자 이름
- `type` (string) - 타입 (fcm, apns, webpush, onesignal)
- `config` (json) - 설정 정보 (API 키, 인증서 등)
- `is_default` (boolean) - 기본 제공자 여부
- `priority` (integer) - 우선순위
- `active` (boolean) - 활성화 상태
- `test_mode` (boolean) - 테스트 모드
- `success_count` (integer) - 성공 횟수
- `failure_count` (integer) - 실패 횟수
- `last_used_at` (timestamp) - 마지막 사용 시간
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_email_templates
재사용 가능한 이메일 템플릿을 관리하는 테이블입니다.
HTML, 텍스트, Markdown 형식을 지원하며 변수 치환 기능으로 동적 콘텐츠를 생성하고,
카테고리별 분류와 우선순위 설정으로 템플릿을 체계적으로 관리할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `name` (string) - 템플릿 이름
- `slug` (string) - 고유 식별자
- `category` (string) - 카테고리 (auth, notification, marketing)
- `subject` (string) - 이메일 제목
- `html_content` (text) - HTML 콘텐츠
- `text_content` (text) - 텍스트 콘텐츠
- `markdown_content` (text) - Markdown 콘텐츠
- `variables` (json) - 사용 가능한 변수 목록
- `from_name` (string) - 발신자 이름
- `from_email` (string) - 발신자 이메일
- `cc` (json) - 참조 목록
- `bcc` (json) - 숨은 참조 목록
- `attachments` (json) - 첨부파일 정보
- `priority` (integer) - 우선순위 (1-5)
- `active` (boolean) - 활성화 상태
- `usage_count` (integer) - 사용 횟수
- `last_used_at` (timestamp) - 마지막 사용 시간
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_email_logs
모든 이메일 발송 내역과 상태를 추적하는 테이블입니다.
발송, 수신, 열람, 클릭 등 전체 라이프사이클을 모니터링하며,
반송(bounce)과 스팸 신고를 추적하여 이메일 평판 관리에 활용할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `to_email` (string) - 수신자 이메일
- `from_email` (string) - 발신자 이메일
- `from_name` (string) - 발신자 이름
- `subject` (string) - 제목
- `body_html` (text) - HTML 본문
- `body_text` (text) - 텍스트 본문
- `template_id` (unsignedBigInteger) - 템플릿 ID
- `template_data` (json) - 템플릿 변수 데이터
- `status` (string) - 상태 (pending, sent, failed, bounced, opened, clicked)
- `provider` (string) - 발송 제공자 (smtp, sendgrid, ses)
- `message_id` (string) - 메시지 ID
- `sent_at` (timestamp) - 발송 시간
- `delivered_at` (timestamp) - 수신 시간
- `opened_at` (timestamp) - 열람 시간
- `clicked_at` (timestamp) - 클릭 시간
- `bounced_at` (timestamp) - 반송 시간
- `bounce_type` (string) - 반송 타입
- `spam_reported_at` (timestamp) - 스팸 신고 시간
- `unsubscribed_at` (timestamp) - 구독 취소 시간
- `error_message` (text) - 에러 메시지
- `attempts` (integer) - 발송 시도 횟수
- `metadata` (json) - 추가 메타데이터
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_email_tracking
이메일 열람 및 링크 클릭을 추적하는 테이블입니다.
픽셀 트래킹과 링크 리다이렉션을 통해 이메일 마케팅 효과를 측정하며,
수신자별 상세한 인터랙션 데이터를 수집하여 캠페인 성과를 분석합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `email_log_id` (unsignedBigInteger) - 이메일 로그 ID
- `event_type` (string) - 이벤트 타입 (open, click, bounce, unsubscribe, spam)
- `tracking_token` (string) - 추적용 고유 토큰
- `link_url` (string) - 클릭한 링크 URL
- `link_name` (string) - 링크 이름/설명
- `link_position` (integer) - 이메일 내 링크 위치
- `ip_address` (string) - 접속 IP 주소
- `user_agent` (string) - 브라우저/디바이스 정보
- `device_type` (string) - 디바이스 타입 (desktop, mobile, tablet)
- `browser` (string) - 브라우저 종류
- `os` (string) - 운영체제
- `country_code` (string) - 국가 코드
- `city` (string) - 도시명
- `timezone` (string) - 시간대
- `metadata` (json) - 추가 메타데이터
- `referrer` (string) - 리퍼러 URL
- `count` (integer) - 이벤트 발생 횟수
- `tracked_at` (timestamp) - 추적 시간
- `first_tracked_at` (timestamp) - 최초 추적 시간
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_sms_providers
Twilio, Vonage, AWS SNS 등 SMS 서비스 제공자를 관리하는 테이블입니다.
국가별 라우팅과 발송 속도 제한을 설정하여 비용을 최적화하며,
다중 제공자 설정으로 장애 시 자동 전환(failover)을 지원합니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `name` (string) - 제공자 이름
- `provider` (string) - 제공자 타입 (twilio, vonage, aws_sns)
- `config` (json) - API 설정 (키, 시크릿, 발신번호 등)
- `is_default` (boolean) - 기본 제공자 여부
- `priority` (integer) - 우선순위
- `country_codes` (json) - 지원 국가 코드 목록
- `rate_limit` (integer) - 분당 발송 제한
- `cost_per_sms` (decimal) - SMS당 비용
- `active` (boolean) - 활성화 상태
- `test_mode` (boolean) - 테스트 모드
- `success_count` (integer) - 성공 횟수
- `failure_count` (integer) - 실패 횟수
- `total_cost` (decimal) - 총 비용
- `last_used_at` (timestamp) - 마지막 사용 시간
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

#### admin_sms_sends
SMS 발송 내역과 결과를 기록하는 테이블입니다.
발송 상태, 비용, 에러 메시지를 추적하여 발송 성공률을 모니터링하며,
2FA 인증, 마케팅, 알림 등 용도별 통계를 제공하여 SMS 사용을 최적화할 수 있습니다.

**컬럼:**
- `id` (bigIncrements) - 기본 키
- `to_number` (string) - 수신 번호
- `from_number` (string) - 발신 번호
- `message` (text) - 메시지 내용
- `provider_id` (unsignedBigInteger) - 제공자 ID
- `provider` (string) - 실제 사용된 제공자
- `status` (string) - 상태 (pending, sending, sent, delivered, failed)
- `message_id` (string) - 제공자 메시지 ID
- `purpose` (string) - 용도 (2fa, notification, marketing)
- `cost` (decimal) - 발송 비용
- `sent_at` (timestamp) - 발송 시간
- `delivered_at` (timestamp) - 수신 확인 시간
- `failed_at` (timestamp) - 실패 시간
- `error_code` (string) - 에러 코드
- `error_message` (text) - 에러 메시지
- `attempts` (integer) - 발송 시도 횟수
- `metadata` (json) - 추가 메타데이터
- `created_at` (timestamp) - 생성 시간
- `updated_at` (timestamp) - 수정 시간

---

## 관련 문서

- [라우트 구현 현황](./route.md) - 라우트와 테이블 매핑, 구현 상태 확인