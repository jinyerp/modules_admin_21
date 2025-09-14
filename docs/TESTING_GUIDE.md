# 관리자 기능 테스트 가이드

이 문서는 구현된 관리자 기능들을 테스트하고 확인하는 방법을 상세히 안내합니다.

## 📋 목차
1. [환경 설정](#환경-설정)
2. [보안 관리 기능](#보안-관리-기능)
3. [알림 관리 기능](#알림-관리-기능)
4. [사용자 관리 기능](#사용자-관리-기능)
5. [통합 테스트 시나리오](#통합-테스트-시나리오)

---

## 환경 설정

### 1. 초기 설정
```bash
# 의존성 설치
composer install
npm install

# 환경 설정 파일 복사
cp .env.example .env

# 애플리케이션 키 생성
php artisan key:generate

# 데이터베이스 마이그레이션
php artisan migrate:fresh --seed

# 개발 서버 실행
composer run dev
```

### 2. 관리자 계정 생성
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'is_admin' => true,
]);
```

### 3. 접속 정보
- **URL**: http://localhost:8000/admin
- **이메일**: admin@example.com
- **비밀번호**: password

---

## 보안 관리 기능

### 1. IP 화이트리스트 관리
**경로**: `/admin/security/ip-whitelist`

#### 테스트 방법:
1. 관리자 패널 접속
2. 사이드바에서 "보안 관리" > "IP 화이트리스트" 클릭
3. 새 IP 추가:
   - "추가" 버튼 클릭
   - IP 주소: `192.168.1.100`
   - 설명: `본사 사무실`
   - 활성화 체크
   - 저장
4. IP 수정 테스트:
   - 목록에서 IP 선택
   - "수정" 버튼 클릭
   - 설명 변경 후 저장
5. IP 삭제 테스트:
   - 목록에서 IP 선택
   - "삭제" 버튼 클릭
   - 확인 팝업에서 "예" 선택

#### 확인 사항:
- ✅ IP 추가 시 목록에 즉시 반영
- ✅ 중복 IP 입력 시 오류 메시지 표시
- ✅ 잘못된 IP 형식 입력 시 유효성 검사
- ✅ 활성/비활성 토글 작동

### 2. IP 블랙리스트 관리
**경로**: `/admin/ipblacklist`

#### 테스트 방법:
1. 사이드바에서 "IP 블랙리스트" 클릭
2. 차단할 IP 추가:
   - IP 주소: `203.0.113.0`
   - 이유: `의심스러운 활동`
   - 차단 기간: `영구`
3. 임시 차단 테스트:
   - IP 주소: `198.51.100.0`
   - 차단 기간: `24시간`
   - 자동 해제 시간 확인

#### 확인 사항:
- ✅ 차단된 IP에서 접속 시도 시 접근 거부
- ✅ 임시 차단 만료 후 자동 해제
- ✅ 차단 로그 기록 확인

### 3. IP 추적 관리
**경로**: `/admin/iptracking`

#### 테스트 방법:
1. 사이드바에서 "IP 추적 관리" 클릭
2. 실시간 추적 데이터 확인:
   - 접속 IP별 요청 횟수
   - 마지막 접속 시간
   - 지역 정보 (GeoIP)
3. 필터링 테스트:
   - 날짜 범위 선택
   - IP 주소로 검색
   - 상태별 필터링

#### 확인 사항:
- ✅ 실시간 IP 추적 데이터 표시
- ✅ 의심스러운 패턴 자동 감지
- ✅ 차트 및 통계 표시

### 4. CAPTCHA 로그
**경로**: `/admin/user/captcha/logs`

#### 테스트 방법:
1. 로그인 페이지에서 잘못된 비밀번호로 3회 시도
2. CAPTCHA 표시 확인
3. 관리자 패널에서 CAPTCHA 로그 확인:
   - 실패한 시도 기록
   - IP 주소별 통계
   - 성공/실패 비율

#### 확인 사항:
- ✅ CAPTCHA 시도 자동 기록
- ✅ 통계 데이터 정확성
- ✅ 필터링 및 검색 기능

### 5. 사용자 활동 로그
**경로**: `/admin/user/logs`

#### 테스트 방법:
1. 다른 브라우저에서 사용자 계정으로 로그인
2. 여러 작업 수행 (프로필 수정, 비밀번호 변경 등)
3. 관리자 패널에서 활동 로그 확인

#### 확인 사항:
- ✅ 모든 사용자 활동 기록
- ✅ 상세 정보 (IP, User Agent, 시간)
- ✅ 활동 유형별 필터링

### 6. 비밀번호 로그
**경로**: `/admin/user/password/logs`

#### 테스트 방법:
1. 사용자 계정에서 비밀번호 변경
2. 잘못된 비밀번호로 로그인 시도
3. 관리자 패널에서 로그 확인

#### 확인 사항:
- ✅ 비밀번호 변경 기록
- ✅ 실패한 로그인 시도 기록
- ✅ 계정 잠금 상태 표시

### 7. 세션 관리
**경로**: `/admin/user/sessions`

#### 테스트 방법:
1. 여러 디바이스/브라우저에서 로그인
2. 관리자 패널에서 활성 세션 확인
3. 특정 세션 강제 종료 테스트

#### 확인 사항:
- ✅ 활성 세션 실시간 표시
- ✅ 세션별 상세 정보
- ✅ 원격 세션 종료 기능

### 8. 2단계 인증 관리
**경로**: `/admin/user/2fa`

#### 테스트 방법:
1. 사용자별 2FA 상태 확인
2. 2FA 강제 활성화 테스트:
   - 특정 사용자 선택
   - "2FA 활성화 요구" 설정
3. 2FA 방법 변경 테스트:
   - SMS → 이메일
   - 이메일 → TOTP

#### 확인 사항:
- ✅ 2FA 상태 일괄 관리
- ✅ 백업 코드 재생성
- ✅ 2FA 리셋 기능

---

## 알림 관리 기능

### 1. 이메일 템플릿 관리
**경로**: `/admin/settings/emailtemplates`

#### 테스트 방법:
1. 새 템플릿 생성:
   - 템플릿 이름: `welcome_email`
   - 제목: `환영합니다, {{name}}님!`
   - 내용: HTML 에디터로 작성
   - 변수: `{{name}}`, `{{email}}`, `{{date}}`
2. 템플릿 미리보기:
   - 테스트 데이터 입력
   - 미리보기 버튼 클릭
3. 테스트 발송:
   - 수신 이메일 입력
   - 테스트 발송 클릭

#### 확인 사항:
- ✅ 변수 치환 정상 작동
- ✅ HTML/텍스트 버전 모두 지원
- ✅ 미리보기 정확성
- ✅ 실제 이메일 수신 확인

### 2. 메일 설정
**경로**: `/admin/settings/mail`

#### 테스트 방법:
1. SMTP 설정:
   ```
   호스트: smtp.gmail.com
   포트: 587
   암호화: TLS
   사용자명: your-email@gmail.com
   비밀번호: app-password
   ```
2. 연결 테스트:
   - "연결 테스트" 버튼 클릭
   - 성공/실패 메시지 확인
3. 테스트 메일 발송:
   - 수신자 이메일 입력
   - "테스트 메일 발송" 클릭

#### 확인 사항:
- ✅ SMTP 연결 성공
- ✅ 테스트 메일 수신
- ✅ 오류 시 상세 메시지 표시

### 3. SMS 제공자 설정
**경로**: `/admin/sms/provider`

#### Twilio 설정 테스트:
1. Twilio 계정 정보 입력:
   ```
   Account SID: ACxxxxxxxxxxxxx
   Auth Token: xxxxxxxxxx
   From Number: +1234567890
   ```
2. 연결 테스트
3. 테스트 SMS 발송:
   - 수신 번호: `+821012345678`
   - 메시지: `테스트 메시지입니다.`

#### 다른 제공자 테스트:
- **Vonage (Nexmo)**
- **AWS SNS**
- **Aligo (한국)**

#### 확인 사항:
- ✅ 각 제공자별 연결 성공
- ✅ SMS 실제 수신 확인
- ✅ 발송 로그 기록

### 4. SMS 발송 로그
**경로**: `/admin/sms/send`

#### 테스트 방법:
1. SMS 발송 후 로그 확인
2. 상태별 필터링:
   - 성공 (sent)
   - 실패 (failed)
   - 대기중 (pending)
3. 재발송 테스트:
   - 실패한 SMS 선택
   - "재발송" 버튼 클릭

#### 확인 사항:
- ✅ 모든 SMS 발송 기록
- ✅ 상세 오류 메시지
- ✅ 재발송 기능 작동

### 5. 알림 규칙 설정
**경로**: `/admin/settings/notifications`

#### 테스트 방법:
1. 새 규칙 생성:
   ```
   이벤트: 로그인 실패
   조건: 3회 이상
   알림 채널: 이메일 + SMS
   수신자: 관리자
   ```
2. 규칙 테스트:
   - 조건 충족 시나리오 실행
   - 알림 수신 확인
3. 멀티채널 테스트:
   - Slack 웹훅 설정
   - Discord 웹훅 설정
   - Teams 웹훅 설정

#### 확인 사항:
- ✅ 조건 기반 알림 발송
- ✅ 멀티채널 동시 발송
- ✅ 알림 히스토리 기록

---

## 사용자 관리 기능

### 1. 사용자 유형 관리
**경로**: `/admin/user/type`

#### 테스트 방법:
1. 새 사용자 유형 생성:
   - 이름: `Premium User`
   - 권한 설정
   - 설명 추가
2. 사용자에게 유형 할당
3. 권한 테스트

#### 확인 사항:
- ✅ 사용자 유형별 권한 분리
- ✅ 일괄 유형 변경
- ✅ 권한 상속 확인

### 2. 아바타 관리
**경로**: `/admin/user/avatar`

#### 테스트 방법:
1. 기본 아바타 설정
2. 사용자별 아바타 업로드
3. 아바타 정책 설정:
   - 최대 크기: 2MB
   - 허용 형식: JPG, PNG
   - 자동 리사이징

#### 확인 사항:
- ✅ 이미지 업로드 및 표시
- ✅ 크기/형식 제한 작동
- ✅ 자동 최적화

---

## 통합 테스트 시나리오

### 시나리오 1: 보안 위협 대응
1. **비정상 로그인 시도**
   - 같은 IP에서 5회 연속 실패
   - CAPTCHA 자동 활성화 확인
   - IP 자동 차단 확인
   - 관리자 알림 발송 확인

2. **계정 복구 프로세스**
   - 계정 잠금 상태 확인
   - 이메일로 잠금 해제 링크 발송
   - 링크 클릭 후 복구 확인
   - 2FA 재설정 옵션 제공

### 시나리오 2: 대량 알림 발송
1. **이벤트 발생**
   - 시스템 점검 공지
   - 모든 사용자에게 알림 발송

2. **발송 프로세스**
   - 이메일 템플릿 선택
   - SMS 동시 발송 설정
   - 발송 진행률 모니터링
   - 실패 건 자동 재시도

### 시나리오 3: 실시간 모니터링
1. **대시보드 확인**
   - 실시간 접속자 수
   - 활성 세션 현황
   - 최근 보안 이벤트

2. **이상 패턴 감지**
   - 특정 IP의 과도한 요청
   - 자동 차단 조치
   - 관리자 알림 발송

---

## 테스트 체크리스트

### 보안 기능
- [ ] IP 화이트리스트 추가/수정/삭제
- [ ] IP 블랙리스트 관리
- [ ] IP 추적 및 모니터링
- [ ] CAPTCHA 작동 확인
- [ ] 활동 로그 기록
- [ ] 비밀번호 정책 적용
- [ ] 세션 관리
- [ ] 2FA 설정 및 테스트

### 알림 기능
- [ ] 이메일 템플릿 생성/수정
- [ ] SMTP 설정 및 테스트
- [ ] SMS 제공자 설정
- [ ] SMS 발송 테스트
- [ ] 알림 규칙 생성
- [ ] 멀티채널 알림 테스트
- [ ] 웹훅 연동 (Slack/Discord/Teams)

### 사용자 관리
- [ ] 사용자 CRUD 작업
- [ ] 사용자 유형 관리
- [ ] 아바타 업로드/관리
- [ ] 권한 설정 및 확인

### 통합 테스트
- [ ] 보안 위협 시나리오
- [ ] 대량 알림 시나리오
- [ ] 실시간 모니터링 시나리오

---

## 문제 해결

### 일반적인 문제

#### 1. 메일 발송 실패
```bash
# .env 파일 확인
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# 캐시 클리어
php artisan config:cache
php artisan queue:restart
```

#### 2. SMS 발송 실패
```bash
# Twilio 자격 증명 확인
php artisan tinker
>>> $twilio = new \Twilio\Rest\Client($sid, $token);
>>> $twilio->messages->create('+821012345678', [
    'from' => '+15017122661',
    'body' => 'Test message'
]);
```

#### 3. 2FA 문제
```bash
# 사용자 2FA 리셋
php artisan tinker
>>> $user = User::find(1);
>>> $user->two_factor_enabled = false;
>>> $user->two_factor_secret = null;
>>> $user->save();
```

#### 4. 세션 문제
```bash
# 모든 세션 클리어
php artisan session:flush

# 특정 사용자 세션 클리어
php artisan tinker
>>> DB::table('sessions')->where('user_id', 1)->delete();
```

---

## 성능 최적화 팁

### 1. 캐싱 활성화
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. 큐 워커 실행
```bash
# 백그라운드 작업용
php artisan queue:work --daemon
```

### 3. 로그 로테이션 설정
```bash
# 로그 파일 정리
truncate -s 0 storage/logs/laravel.log
```

### 4. 데이터베이스 인덱싱
```sql
-- 자주 조회되는 필드에 인덱스 추가
CREATE INDEX idx_ip_tracking_ip ON admin_ip_tracking(ip_address);
CREATE INDEX idx_user_logs_user ON admin_user_logs(user_id);
CREATE INDEX idx_sessions_user ON admin_sessions(user_id);
```

---

## 추가 리소스

- [Laravel 공식 문서](https://laravel.com/docs)
- [Livewire 문서](https://livewire.laravel.com)
- [Tailwind CSS 문서](https://tailwindcss.com/docs)
- [프로젝트 GitHub](https://github.com/your-repo)

---

## 지원 및 문의

문제가 발생하거나 추가 지원이 필요한 경우:
- 이메일: admin@example.com
- Slack: #admin-support
- 이슈 트래커: GitHub Issues

---

*마지막 업데이트: 2025년 9월 13일*