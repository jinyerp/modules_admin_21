# 마이그레이션 파일 정리 및 데이터베이스 구조 비교

## 1. 마이그레이션 파일 이동 완료
- `/database/migrations/`에서 `/jiny/admin/database/migrations/`로 이동
  - ✅ 2025_09_02_060324_add_last_login_at_to_users_table.php
  - ✅ 2025_09_02_081927_add_login_count_to_users_table.php
  - ✅ 2025_09_03_083034_add_last_activity_at_to_users_table.php
  - ✅ 2025_09_04_142057_add_password_expiry_to_users_table.php
  - ✅ 2025_09_04_161125_add_password_force_change_to_users_table.php

## 2. 중복 파일 삭제
- ❌ 2025_09_02_create_password_logs_table.php (중복)
- ❌ 2025_09_04_143749_create_admin_user_passwords_table.php (중복)
- ❌ 2025_09_04_fix_admin_password_logs_nullable_columns.php (중복)
- ❌ 2025_09_04_fix_admin_password_logs_nullable.php (중복)

## 3. 통합 마이그레이션 파일 생성
- ✅ 2025_09_01_000000_create_admin_tables.php (새로 생성)
  - 모든 admin 관련 테이블과 컬럼을 하나의 파일로 통합
  - 중복 체크 로직 포함 (hasTable, hasColumn)

## 4. 현재 데이터베이스 구조

### users 테이블 (확인됨)
- ✅ id, name, email, email_verified_at, password
- ✅ remember_token, created_at, updated_at
- ✅ isAdmin, utype
- ✅ last_login_at, login_count, last_activity_at
- ✅ two_factor_secret, two_factor_recovery_codes
- ✅ two_factor_confirmed_at, two_factor_enabled, last_2fa_used_at
- ✅ password_changed_at, password_expires_at, password_expiry_days
- ✅ password_expiry_notified, password_must_change, force_password_change
- ✅ avatar, avatar_original_name

### admin 관련 테이블 (확인됨)
- ✅ admin_user_types
- ✅ admin_user_logs
- ✅ admin_password_logs
- ✅ admin_user_sessions
- ✅ admin_user_password_logs
- ✅ admin_user2fas (별도 관리)
- ✅ admin_sessions (별도 관리)

## 5. 최종 마이그레이션 파일 구조
```
jiny/admin/database/migrations/
├── 2025_08_31_181000_create_admin_user_type_table.php
├── 2025_09_01_000000_create_admin_tables.php (통합 파일)
├── 2025_09_01_000001_add_admin_fields_to_users_table.php
├── 2025_09_01_000003_add_user_count_to_admin_user_types_table.php
├── 2025_09_01_165754_create_admin_user_logs_table.php
├── 2025_09_02_060324_add_last_login_at_to_users_table.php
├── 2025_09_02_081927_add_login_count_to_users_table.php
├── 2025_09_02_100000_add_2fa_columns_to_users_table.php
├── 2025_09_02_150000_add_2fa_columns_to_admin_user_logs_table.php
├── 2025_09_02_155740_create_admin_password_logs_table.php
├── 2025_09_02_200000_create_admin_user_sessions_table.php
├── 2025_09_03_083034_add_last_activity_at_to_users_table.php
├── 2025_09_04_142057_add_password_expiry_to_users_table.php
├── 2025_09_04_154309_create_admin_user_password_logs_table.php
├── 2025_09_04_161125_add_password_force_change_to_users_table.php
└── 2025_09_07_030443_add_avatar_to_users_table.php
```

## 6. 권장사항
1. 새로운 프로젝트에서는 `2025_09_01_000000_create_admin_tables.php` 하나만 실행
2. 기존 프로젝트는 현재 구조 유지
3. 개별 마이그레이션 파일들은 호환성을 위해 보관

## 7. 데이터베이스와 마이그레이션 동기화 상태
✅ **완전 동기화됨** - 모든 테이블과 컬럼이 마이그레이션 파일과 일치