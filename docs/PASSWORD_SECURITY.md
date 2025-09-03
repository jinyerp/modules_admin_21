# 비밀번호 보안 시스템 문서

## 목차
1. [개요](#개요)
2. [기능](#기능)
3. [설정](#설정)
4. [사용법](#사용법)
5. [콘솔 명령어](#콘솔-명령어)
6. [관리자 페이지](#관리자-페이지)
7. [데이터베이스 구조](#데이터베이스-구조)
8. [API 참조](#api-참조)

## 개요

@jiny/admin 패키지의 비밀번호 보안 시스템은 무차별 대입 공격(Brute Force Attack)을 방지하고 관리자 계정을 보호하기 위한 포괄적인 솔루션입니다.

### 주요 특징
- 🔒 **자동 차단**: 5회 이상 로그인 실패 시 자동 차단
- 📊 **실시간 모니터링**: 비밀번호 시도 실시간 추적
- 🛠 **관리 도구**: 웹 인터페이스와 콘솔 명령 제공
- 📝 **상세 로깅**: 모든 시도와 차단 기록 저장

## 기능

### 1. 로그인 시도 추적
- 이메일별, IP별 로그인 시도 기록
- 브라우저 정보 (종류, 버전, 플랫폼) 수집
- 24시간 단위로 시도 횟수 누적

### 2. 자동 차단 시스템
- **차단 조건**: 동일 이메일/IP에서 5회 이상 실패
- **차단 범위**: 이메일과 IP 조합으로 차단
- **차단 효과**: 로그인 페이지 접근 제한

### 3. 경고 시스템
- 3회 실패 시점부터 남은 시도 횟수 표시
- 차단 시 명확한 안내 메시지 제공

## 설정

### 1. 마이그레이션 실행
```bash
php artisan migrate
```

### 2. 환경 설정 (선택사항)
`.env` 파일에서 설정 가능한 옵션:

```env
# 차단 임계값 (기본: 5)
PASSWORD_MAX_ATTEMPTS=5

# 시도 기록 유효 시간 (시간 단위, 기본: 24)
PASSWORD_ATTEMPT_DECAY_HOURS=24

# 자동 차단 해제 시간 (시간 단위, 기본: 없음)
PASSWORD_AUTO_UNBLOCK_HOURS=48
```

## 사용법

### 로그인 프로세스

1. **정상 로그인**
   - 이메일과 비밀번호 입력
   - 2FA 활성화 시 추가 인증
   - 대시보드로 이동

2. **로그인 실패**
   - 실패 횟수 자동 기록
   - 3회 이상: 경고 메시지 표시
   - 5회 이상: 자동 차단

3. **차단된 경우**
   - 로그인 불가능
   - 관리자 문의 안내
   - 콘솔 명령으로만 해제 가능

### 차단 해제 방법

#### 방법 1: 웹 인터페이스 (관리자)
1. `/admin/user/password/logs` 접속
2. 차단된 항목 확인
3. "차단 해제" 버튼 클릭

#### 방법 2: 콘솔 명령 (시스템 관리자)
```bash
php artisan admin:password-unblock email@example.com
```

## 콘솔 명령어

### 1. `admin:password-unblock` - 차단 해제

#### 기본 사용법
```bash
# 차단된 목록 보기
php artisan admin:password-unblock --show

# 특정 이메일 차단 해제
php artisan admin:password-unblock email@example.com

# 특정 IP 차단 해제
php artisan admin:password-unblock --ip=192.168.1.1

# 모든 차단 해제
php artisan admin:password-unblock --all

# 인터랙티브 모드 (선택하여 해제)
php artisan admin:password-unblock
```

#### 출력 예시
```
+----+---------------------+-----------+-----------+---------------------+
| ID | 이메일              | IP 주소   | 시도 횟수 | 차단 시간           |
+----+---------------------+-----------+-----------+---------------------+
| 1  | user@example.com    | 127.0.0.1 | 5         | 2025-09-02 16:28:35 |
+----+---------------------+-----------+-----------+---------------------+
총 1개의 차단된 시도가 있습니다.
```

### 2. `admin:password-reset` - 시도 기록 초기화

#### 기본 사용법
```bash
# 특정 이메일 시도 초기화
php artisan admin:password-reset email@example.com

# 특정 IP 시도 초기화
php artisan admin:password-reset --ip=192.168.1.1

# 오래된 기록 삭제 (7일 이전)
php artisan admin:password-reset --days=7

# 모든 기록 삭제
php artisan admin:password-reset --all
```

#### 옵션
- `{email}`: 초기화할 이메일 주소
- `--ip`: 초기화할 IP 주소
- `--days`: N일 이전 기록 삭제 (기본: 1)
- `--all`: 모든 기록 삭제

## 관리자 페이지

### 접근 경로
```
/admin/user/password/logs
```

### 기능
1. **목록 보기**
   - 모든 비밀번호 시도 기록 표시
   - 상태별 필터 (failed, blocked, resolved)
   - 검색 기능 (이메일, IP, 브라우저)

2. **상세 정보**
   - 개별 시도 상세 정보
   - 브라우저 및 디바이스 정보
   - 시도 타임라인

3. **관리 기능**
   - 개별 차단 해제
   - 대량 차단 해제
   - 기록 삭제

### 화면 구성

#### 목록 화면
| 컬럼 | 설명 |
|------|------|
| 이메일 | 시도한 이메일 주소 |
| IP 주소 | 접속 IP |
| 브라우저 | 브라우저 종류 및 버전 |
| 시도 횟수 | 누적 시도 횟수 |
| 상태 | failed/blocked/resolved |
| 마지막 시도 | 최근 시도 시간 |
| 작업 | 차단 해제, 상세보기 |

#### 필터 옵션
- **상태**: 전체, 실패, 차단됨, 해결됨
- **시도 횟수**: 1-2회, 3-4회, 5회 이상
- **기간**: 오늘, 7일, 30일, 전체

## 데이터베이스 구조

### `admin_password_logs` 테이블

```sql
CREATE TABLE admin_password_logs (
    id BIGINT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    browser VARCHAR(255),
    platform VARCHAR(255),
    device VARCHAR(255),
    attempt_count INT DEFAULT 1,
    first_attempt_at TIMESTAMP,
    last_attempt_at TIMESTAMP,
    is_blocked BOOLEAN DEFAULT FALSE,
    blocked_at TIMESTAMP NULL,
    unblocked_at TIMESTAMP NULL,
    status VARCHAR(255) DEFAULT 'failed',
    details JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_email_ip (email, ip_address),
    INDEX idx_email_last (email, last_attempt_at),
    INDEX idx_blocked (is_blocked),
    INDEX idx_created (created_at)
);
```

### 컬럼 설명

| 컬럼 | 타입 | 설명 |
|------|------|------|
| `email` | VARCHAR(255) | 시도한 이메일 주소 |
| `user_id` | BIGINT | 존재하는 사용자 ID (있는 경우) |
| `ip_address` | VARCHAR(45) | IPv4/IPv6 주소 |
| `user_agent` | TEXT | 전체 User Agent 문자열 |
| `browser` | VARCHAR(255) | 파싱된 브라우저 이름 |
| `platform` | VARCHAR(255) | OS/플랫폼 정보 |
| `device` | VARCHAR(255) | Desktop/Mobile/Tablet |
| `attempt_count` | INT | 누적 시도 횟수 |
| `first_attempt_at` | TIMESTAMP | 첫 시도 시간 |
| `last_attempt_at` | TIMESTAMP | 마지막 시도 시간 |
| `is_blocked` | BOOLEAN | 차단 여부 |
| `blocked_at` | TIMESTAMP | 차단 시간 |
| `unblocked_at` | TIMESTAMP | 차단 해제 시간 |
| `status` | VARCHAR(255) | failed/blocked/resolved |
| `details` | JSON | 추가 메타데이터 |

## API 참조

### AdminPasswordLog 모델

#### 주요 메서드

```php
// 실패 시도 기록
AdminPasswordLog::recordFailedAttempt($email, $request, $userId = null);

// 차단 여부 확인
AdminPasswordLog::isBlocked($email, $ipAddress);

// 차단 해제
$log->unblock();
```

#### Scopes

```php
// 차단된 기록만
AdminPasswordLog::blocked()->get();

// 활성 기록 (24시간 내)
AdminPasswordLog::active()->get();

// 위험한 시도 (3회 이상)
AdminPasswordLog::dangerous()->get();
```

### 이벤트

시스템은 다음 이벤트를 발생시킵니다:

| 이벤트 | 설명 | 데이터 |
|--------|------|--------|
| `password.failed` | 로그인 실패 | email, ip, attempt_count |
| `password.blocked` | 계정 차단 | email, ip, blocked_at |
| `password.unblocked` | 차단 해제 | email, ip, unblocked_at |

### 로그 기록

모든 활동은 `admin_user_logs` 테이블에 기록됩니다:

```php
// 차단 로그
AdminUserLog::log('password_blocked', null, [
    'email' => $email,
    'ip_address' => $ip,
    'attempts' => $attemptCount,
    'blocked_at' => now()
]);

// 차단 해제 로그
AdminUserLog::log('password_unblocked', null, [
    'email' => $email,
    'ip_address' => $ip,
    'unblocked_by' => $adminEmail,
    'unblocked_at' => now()
]);
```

## 보안 고려사항

### 1. IP 차단 정책
- IP만으로 차단하지 않음 (공용 IP 고려)
- 이메일과 IP 조합으로 차단
- VPN/프록시 사용 고려

### 2. 차단 해제 권한
- 웹: 관리자 권한 필요
- 콘솔: 서버 접근 권한 필요
- 모든 해제 활동 로깅

### 3. 개인정보 보호
- User Agent 암호화 저장 고려
- IP 주소 마스킹 옵션
- 자동 기록 삭제 정책

## 문제 해결

### Q: 관리자가 차단되었을 때?
**A:** 콘솔 명령 사용
```bash
php artisan admin:password-unblock admin@example.com
```

### Q: 차단이 자동 해제되나요?
**A:** 기본적으로 수동 해제만 가능. 자동 해제 설정 가능:
```php
// 24시간 후 자동 해제 (스케줄러 설정 필요)
$schedule->command('admin:password-unblock --expired=24')->hourly();
```

### Q: 차단 임계값 변경?
**A:** `AdminPasswordLog::recordFailedAttempt()` 메서드에서 수정:
```php
// 기본값: 5회
if ($recentLog->attempt_count >= 5 && !$recentLog->is_blocked) {
    // 차단 처리
}
```

### Q: 특정 IP 화이트리스트?
**A:** `AdminAuthController::login()` 메서드에 추가:
```php
$whitelist = ['127.0.0.1', '192.168.1.1'];
if (in_array($request->ip(), $whitelist)) {
    // 차단 검사 건너뛰기
}
```

## 업데이트 내역

### v1.0.0 (2025-09-02)
- 초기 릴리스
- 자동 차단 시스템
- 관리자 페이지
- 콘솔 명령어

## 라이선스

이 시스템은 @jiny/admin 패키지의 일부로 제공됩니다.

## 지원

문제가 있거나 기능 요청이 있는 경우:
- GitHub Issues: [jiny/admin/issues](https://github.com/jiny/admin/issues)
- 이메일: support@jiny.dev