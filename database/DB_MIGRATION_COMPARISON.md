# 데이터베이스와 마이그레이션 파일 비교 분석

## 📊 비교 결과 요약

실제 데이터베이스와 마이그레이션 파일 간에 **상당한 차이점**이 발견되었습니다.

## 1. users 테이블
### ✅ 일치하는 컬럼
- 기본 필드: id, name, email, password, created_at, updated_at
- Admin 필드: isAdmin, utype
- 로그인 관련: last_login_at, login_count, last_activity_at
- 2FA 관련: two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, two_factor_enabled
- 패스워드 관련: password_changed_at, password_expires_at, force_password_change
- 아바타: avatar

### ❌ 누락된 컬럼 (DB에는 있지만 마이그레이션에 없음)
- `avatar_original_name` (varchar) - 원본 파일명 저장용

## 2. admin_user_types 테이블
### ✅ 일치하는 컬럼
- id, code, name, description, level, enable, pos, created_at, updated_at

### ❌ 차이점
- DB: `user_count` (INTEGER)
- 마이그레이션: `cnt` (integer)
- **누락**: `badge_color`, `permissions`, `settings` (마이그레이션에만 있음)

## 3. admin_user_logs 테이블
### ❌ 큰 차이점 발견
**DB 구조:**
- email, name, action, details, session_id, logged_at
- two_factor_required, two_factor_attempts

**마이그레이션 구조:**
- event_type (DB의 action), extra_data (DB의 details)
- email, name, session_id, logged_at 등 누락

## 4. admin_password_logs 테이블
### ❌ 많은 추가 컬럼 존재
**DB에만 있는 컬럼:**
- browser, platform, device (사용자 환경 정보)
- first_attempt_at, is_blocked, blocked_at, unblocked_at (차단 관리)
- status, details, action, old_password_hash, metadata

**차이점:**
- DB: `blocked_at`
- 마이그레이션: `blocked_until`

## 5. admin_user_sessions 테이블
### ❌ 차이점
**DB에만 있는 컬럼:**
- last_activity (마이그레이션: last_activity_at)
- login_at, browser, browser_version, platform, device
- two_factor_used, payload

## 6. admin_user_password_logs 테이블
### ❌ 구조 완전히 다름
**DB 구조:**
- action, description, performed_by

**마이그레이션 구조:**
- old_password_hash, new_password_hash, changed_by, change_reason

## 7. 기타 테이블
### ✅ admin_user2fas, admin_sessions
- 별도 관리 테이블로 정상

## 📝 권장 조치사항

1. **즉시 수정 필요**
   - `2025_09_08_000000_fix_missing_columns.php` 마이그레이션 실행
   - 이 파일은 모든 누락된 컬럼과 차이점을 수정합니다

2. **통합 마이그레이션 업데이트**
   - `2025_09_01_000000_create_admin_tables.php` 수정 필요
   - 실제 DB 구조와 일치하도록 업데이트

3. **컬럼명 통일**
   - user_count vs cnt
   - event_type vs action
   - extra_data vs details
   - blocked_until vs blocked_at
   - last_activity_at vs last_activity

4. **문서화**
   - 각 테이블의 용도와 컬럼 설명 추가
   - 변경 이력 관리

## 🔍 발견된 주요 문제

1. **명명 규칙 불일치**: 같은 목적의 컬럼이 다른 이름 사용
2. **누락된 기능**: 브라우저 정보, 차단 관리 등 중요 기능 누락
3. **구조 차이**: admin_user_password_logs 테이블 구조 완전히 다름
4. **타입 불일치**: 일부 컬럼의 데이터 타입 차이

## ✅ 해결 방법

1. `2025_09_08_000000_fix_missing_columns.php` 실행으로 차이점 해결
2. 향후 마이그레이션 작성 시 실제 DB 구조 확인 필수
3. 테스트 환경에서 전체 마이그레이션 재실행 테스트 권장