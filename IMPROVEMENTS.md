# @jiny/admin 개선 목록

## 구현 완료 ✅

### 1. IP 접근 제한 기능 (2025-09-10)
- **상태**: 구현 완료 
- **문서**: [IP 화이트리스트 기능 상세 문서](docs/features/ip-whitelist.md)

### 2. 메일 발송 시스템 (2025-09-10)
- **상태**: 구현 완료
- **구현된 기능**:
  - ✅ SMTP 메일 설정 관리 UI
  - ✅ 테스트 메일 발송 기능
  - ✅ 이메일 템플릿 관리 시스템
  - ✅ 변수 치환 시스템 ({{variable}} 형식)
  - ✅ 템플릿 미리보기 기능
  - ✅ 메일 발송 로그 시스템
  - ✅ 발송 상태 추적 (pending/sent/failed/bounced)
  - ✅ 재발송 기능
  - ✅ 알림 규칙 엔진
  - ✅ 이벤트 기반 자동 알림 (로그인 실패, 2FA 변경, IP 차단 등)
  - ✅ Hook 시스템 통합
- **테스트 방법**: 아래 [메일 시스템 테스트 가이드](#메일-시스템-테스트-가이드) 참조

## 미구현 기능 목록 📋

### 보안 강화

#### 2. 로그인 시도 제한 강화
- **현재 상태**: 기본 패스워드 실패 로그만 기록
- **개선 사항**:
  - Google reCAPTCHA 또는 hCaptcha 통합
  - 계정 잠금 시 이메일/SMS 알림
  - 잠금 해제 링크 생성
  - IP별 시도 제한 (현재는 계정별만)

#### 3. 2단계 인증 (2FA) 고도화
- **현재 상태**: 기본 TOTP 구현
- **개선 사항**:
  - SMS 기반 2FA 옵션
  - 이메일 기반 2FA 옵션
  - 백업 코드 재생성 기능
  - 복구 코드 다운로드

### 모니터링 & 분석

#### 4. 실시간 대시보드 위젯
- **개선 사항**:
  - 실시간 접속자 수 (WebSocket)
  - 최근 24시간 에러 차트
  - CPU/메모리 사용률 모니터링
  - 데이터베이스 쿼리 성능 지표
  - API 응답 시간 그래프

#### 5. 감사 로그 고도화
- **현재 상태**: 기본 활동 로그 (`AdminUserLog`)
- **개선 사항**:
  - 데이터 변경 전/후 값 비교 (diff 뷰)
  - 중요 작업 승인 워크플로우
  - 로그 검색 필터 고도화
  - 로그 내보내기 (Excel/PDF)
  - 정기 보고서 자동 생성

### 알림 시스템

#### 6. 통합 알림 센터
- **일부 구현**: 이메일 알림 시스템 구현 완료
- **구현된 기능**:
  - ✅ 이메일 템플릿 관리자
  - ✅ 알림 규칙 설정 (조건부 알림)
  - ✅ 알림 히스토리 및 재전송
- **미구현 기능**:
  - Slack/Discord/Teams 웹훅 연동
  - 푸시 알림 (브라우저/모바일)

### 운영 도구

#### 7. 시스템 설정 UI
- **일부 구현**: 메일 설정 관리 구현 완료
- **구현된 기능**:
  - ✅ 메일 설정 테스터
- **미구현 기능**:
  - .env 파일 편집기 (보안 설정 포함)
  - 캐시 관리 (clear, warm-up)
  - 큐 모니터링 및 관리
  - 스케줄러 관리

#### 8. 백업 & 복구
- **개선 사항**:
  - 자동 백업 스케줄링
  - 백업 저장소 관리 (로컬/S3/FTP)
  - 선택적 백업 (DB/파일/설정)
  - 원클릭 복구
  - 백업 무결성 검증

#### 9. 데이터 Import/Export
- **개선 사항**:
  - CSV/Excel 일괄 가져오기
  - 데이터 매핑 UI
  - 검증 및 오류 리포트
  - 진행률 표시 (대용량 처리)
  - 템플릿 생성기

### UX/UI 개선

#### 10. 테마 시스템
- **개선 사항**:
  - 다크/라이트 모드 전환
  - 커스텀 테마 생성기
  - 색상 스킴 프리셋
  - 폰트 크기 조절
  - 레이아웃 커스터마이징

#### 11. 다국어 지원
- **개선 사항**:
  - 언어 파일 관리자
  - 자동 번역 API 연동
  - 사용자별 언어 설정
  - RTL 언어 지원
  - 언어별 날짜/숫자 포맷

### 성능 최적화

#### 12. 고급 캐싱
- **개선 사항**:
  - Redis 클러스터 지원
  - 캐시 태깅 및 무효화 전략
  - 캐시 히트율 모니터링
  - 자동 캐시 워밍
  - CDN 통합

#### 13. 검색 엔진
- **개선 사항**:
  - Elasticsearch/Meilisearch 통합
  - 전문 검색 (Full-text search)
  - 자동 완성 및 제안
  - 검색 분석 및 최적화
  - 검색 결과 필터링/정렬

### 개발자 도구

#### 14. API 문서화
- **개선 사항**:
  - OpenAPI/Swagger 통합
  - API 테스트 도구
  - API 버전 관리
  - Rate Limiting 대시보드
  - API 키 관리

#### 15. 디버깅 도구
- **개선 사항**:
  - SQL 쿼리 분석기
  - 성능 프로파일러
  - 메모리 사용량 추적
  - 느린 쿼리 감지
  - 에러 스택 트레이스 뷰어

## 우선순위 권장사항 🎯

### 단기 (1-2주)
1. 로그인 시도 제한 강화
2. 실시간 대시보드 위젯
3. 시스템 설정 UI

### 중기 (1-2개월)
5. 통합 알림 센터
6. 백업 & 복구
7. 데이터 Import/Export
8. 테마 시스템

### 장기 (3-6개월)
9. 검색 엔진 통합
10. API 문서화
11. 다국어 지원
12. 고급 캐싱

## 기술 스택 제안 💡

### 실시간 기능
- **Laravel Echo** + **Pusher/Soketi**: WebSocket 통신
- **Laravel Reverb**: 자체 호스팅 WebSocket 서버

### 모니터링
- **Laravel Pulse**: 애플리케이션 성능 모니터링
- **Laravel Horizon**: 큐 모니터링
- **Sentry**: 에러 트래킹

### 검색
- **Laravel Scout** + **Meilisearch**: 빠른 검색
- **Elasticsearch**: 고급 검색 기능

### 백업
- **spatie/laravel-backup**: 자동화된 백업
- **AWS S3**: 클라우드 백업 저장소

### 캐싱
- **Redis Cluster**: 분산 캐싱
- **CloudFlare**: CDN 및 엣지 캐싱

## 참고사항 📌

1. 모든 기능은 기존 @jiny/admin 아키텍처와 일관성을 유지해야 함
2. Hook 패턴과 JSON 설정 기반 구조를 따라야 함
3. Livewire 3 컴포넌트로 구현 권장
4. 보안 기능은 최우선으로 구현
5. 성능 영향도를 고려한 점진적 구현

## 메일 시스템 테스트 가이드 📧

### 1. 메일 설정 테스트
1. **메일 설정 페이지 접속**
   ```
   http://localhost:8005/admin/settings/mail
   ```

2. **SMTP 설정 입력**
   - Gmail 예시:
     ```
     메일 드라이버: SMTP
     SMTP 호스트: smtp.gmail.com
     SMTP 포트: 587
     SMTP 사용자명: your-email@gmail.com
     SMTP 비밀번호: 앱 비밀번호 (2단계 인증 필요)
     암호화 방식: TLS
     발신자 이메일: your-email@gmail.com
     발신자 이름: 시스템 관리자
     ```

3. **테스트 메일 발송**
   - "테스트 메일 발송" 버튼 클릭
   - 수신 이메일 주소 입력
   - 메일 수신 확인

### 2. 이메일 템플릿 테스트
1. **템플릿 생성 (코드로 실행)**
   ```php
   php artisan tinker
   
   use Jiny\Admin\App\Services\EmailTemplateService;
   $service = new EmailTemplateService();
   
   // 로그인 실패 템플릿 생성
   $service->createTemplate([
       'name' => '로그인 실패 알림',
       'slug' => 'login_failed',
       'subject' => '{{app_name}} - 로그인 시도 실패',
       'body' => '<h2>로그인 실패 알림</h2>
       <p>{{user_name}}님,</p>
       <p>귀하의 계정에서 {{failed_attempts}}회 로그인 시도가 실패했습니다.</p>
       <p>IP 주소: {{ip_address}}</p>
       <p>시간: {{current_date}}</p>',
       'variables' => ['user_name', 'failed_attempts', 'ip_address'],
       'type' => 'html',
       'is_active' => true
   ]);
   ```

2. **템플릿 미리보기**
   ```php
   $preview = $service->previewTemplate('login_failed');
   echo $preview['body'];
   ```

### 3. 알림 규칙 테스트
1. **알림 규칙 생성**
   ```php
   use Jiny\Admin\App\Services\NotificationService;
   $notifyService = new NotificationService();
   
   $notifyService->createRule([
       'name' => '3회 이상 로그인 실패 알림',
       'event_type' => 'login_failed',
       'conditions' => [
           'failed_attempts' => ['operator' => '>=', 'value' => 3]
       ],
       'template_slug' => 'login_failed',
       'recipient_type' => 'user',
       'recipient_value' => null,
       'is_active' => true,
       'throttle_minutes' => 30
   ]);
   ```

2. **로그인 실패 시뮬레이션**
   - 관리자 로그인 페이지에서 잘못된 비밀번호로 3회 이상 로그인 시도
   - 이메일 알림 수신 확인

### 4. 메일 로그 확인
```php
use Jiny\Admin\App\Services\EmailLogService;
$logService = new EmailLogService();

// 최근 로그 확인
$logs = $logService->getRecentLogs(10);
foreach($logs as $log) {
    echo "To: {$log->to}, Subject: {$log->subject}, Status: {$log->status}\n";
}

// 통계 확인
$stats = $logService->getStatistics();
print_r($stats);
```

### 5. Hook 시스템 테스트
1. **커스텀 Hook 추가 (컨트롤러에서)**
   ```php
   // AdminSettingsMail 컨트롤러에 추가
   protected function hookBeforeSendMail(&$to, &$subject, &$body)
   {
       // 메일 발송 전 처리
       \Log::info("메일 발송 준비: {$to}");
   }
   
   protected function hookAfterSendMail($to, $subject, $success)
   {
       // 메일 발송 후 처리
       if ($success) {
           \Log::info("메일 발송 성공: {$to}");
       }
   }
   ```

### 6. 통합 테스트 시나리오
1. **2FA 변경 알림 테스트**
   - 사용자 2FA 설정 페이지 접속
   - 2FA 활성화/비활성화
   - 이메일 알림 수신 확인

2. **IP 차단 알림 테스트**
   - IP 화이트리스트에서 IP 차단
   - 해당 사용자에게 알림 메일 발송 확인

### 트러블슈팅
- **메일이 발송되지 않는 경우**:
  1. Laravel 로그 확인: `storage/logs/laravel.log`
  2. 메일 설정 확인: `jiny/admin/config/mail.php`
  3. SMTP 자격 증명 확인
  
- **Gmail 사용 시**:
  1. 2단계 인증 활성화 필요
  2. 앱 비밀번호 생성 후 사용
  3. "보안 수준이 낮은 앱 액세스" 허용

## 업데이트 로그 📅

- **2025-09-10**: 
  - IP 접근 제한 기능 구현 완료
  - 메일 발송 시스템 구현 완료
  - 이메일 템플릿 관리 시스템 추가
  - 알림 규칙 엔진 구현
  - 메일 발송 로그 시스템 추가