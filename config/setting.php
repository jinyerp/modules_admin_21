<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Settings
    |--------------------------------------------------------------------------
    |
    | This file contains various configuration options for the admin panel.
    |
    */

    'name' => 'Jiny Admin',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Password Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Define password validation rules and requirements for the admin system.
    |
    */
    'password' => [
        // 최소 길이
        'min_length' => 8,

        // 최대 길이
        'max_length' => 128,

        // 대문자 포함 필수 여부
        'require_uppercase' => true,

        // 소문자 포함 필수 여부
        'require_lowercase' => true,

        // 숫자 포함 필수 여부
        'require_numbers' => true,

        // 특수문자 포함 필수 여부
        'require_special_chars' => true,

        // 허용된 특수문자 목록
        'allowed_special_chars' => '!@#$%^&*()_+-=[]{}|;:,.<>?',

        // 공백 허용 여부
        'allow_spaces' => false,

        // 이전 비밀번호 재사용 방지 (몇 개까지 기억할지)
        'password_history' => 5,

        // 비밀번호 갱신 주기 설정
        // 옵션: 30, 90, 120, 365, 0 (0은 만료 없음)
        'expiry_days_options' => [
            0 => '없음',
            30 => '30일',
            90 => '90일',
            120 => '120일',
            365 => '365일 (1년)',
        ],

        // 비밀번호 만료 기간 (일 단위, 0은 만료 없음)
        'expiry_days' => 90,

        // 비밀번호 만료 알림 기간 (만료 며칠 전부터 알림)
        'expiry_warning_days' => 7,

        // 로그인 실패 시 계정 잠금
        'lockout' => [
            // 최대 시도 횟수
            'max_attempts' => 5,

            // 잠금 시간 (분 단위)
            'lockout_duration' => 30,

            // DB에 기록 시작할 실패 횟수 (이 횟수부터 DB에 기록)
            'log_after_attempts' => 5,

            // 실패 카운트 유지 시간 (초 단위, 캐시 TTL)
            'attempt_cache_ttl' => 3600, // 1시간

            // 경고 메시지 표시 시작 횟수
            'warning_after_attempts' => 3,
        ],

        // 비밀번호 강도 체크
        'strength' => [
            // 최소 강도 레벨 (1: weak, 2: fair, 3: good, 4: strong, 5: very strong)
            'min_level' => 3,

            // 일반적인 비밀번호 체크
            'check_common_passwords' => true,

            // 사용자 정보와 유사성 체크 (이름, 이메일 등)
            'check_user_similarity' => true,

            // 연속된 문자/숫자 체크 (예: abc, 123)
            'check_sequential' => true,

            // 반복된 문자 체크 (예: aaa, 111)
            'check_repeated' => true,
            'max_repeated_chars' => 3,
        ],

        // 비밀번호 복잡도 규칙 메시지
        'messages' => [
            'min_length' => '비밀번호는 최소 :min자 이상이어야 합니다.',
            'max_length' => '비밀번호는 최대 :max자를 초과할 수 없습니다.',
            'require_uppercase' => '비밀번호는 최소 1개의 대문자를 포함해야 합니다.',
            'require_lowercase' => '비밀번호는 최소 1개의 소문자를 포함해야 합니다.',
            'require_numbers' => '비밀번호는 최소 1개의 숫자를 포함해야 합니다.',
            'require_special_chars' => '비밀번호는 최소 1개의 특수문자를 포함해야 합니다.',
            'no_spaces' => '비밀번호에 공백을 포함할 수 없습니다.',
            'password_used' => '이전에 사용한 비밀번호는 재사용할 수 없습니다.',
            'too_common' => '너무 일반적인 비밀번호입니다.',
            'too_similar' => '사용자 정보와 너무 유사한 비밀번호입니다.',
            'sequential_chars' => '연속된 문자나 숫자를 사용할 수 없습니다.',
            'repeated_chars' => '동일한 문자를 :max개 이상 연속으로 사용할 수 없습니다.',
            'weak_password' => '비밀번호 강도가 너무 약합니다.',
        ],

        // 비밀번호 생성 도구 설정
        'generator' => [
            // 자동 생성 비밀번호 길이
            'default_length' => 16,

            // 생성 시 포함할 문자 유형
            'include_uppercase' => true,
            'include_lowercase' => true,
            'include_numbers' => true,
            'include_special' => true,

            // 혼동하기 쉬운 문자 제외 (0, O, l, 1 등)
            'exclude_ambiguous' => true,
            'ambiguous_chars' => '0O1lI',
        ],

        // 2단계 인증 설정
        'two_factor' => [
            // 2단계 인증 사용 여부
            'enabled' => false,

            // 2단계 인증 강제 여부
            'required' => false,

            // 2단계 인증 방법 (totp, sms, email)
            'methods' => ['totp', 'email'],

            // 백업 코드 개수
            'backup_codes' => 8,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CAPTCHA Settings
    |--------------------------------------------------------------------------
    |
    | CAPTCHA 관련 설정 (Google reCAPTCHA, hCaptcha 지원)
    |
    */
    'captcha' => [
        // CAPTCHA 기능 활성화 여부
        'enabled' => env('ADMIN_CAPTCHA_ENABLED', false),
        
        // CAPTCHA 드라이버 (recaptcha, hcaptcha)
        'driver' => env('ADMIN_CAPTCHA_DRIVER', 'recaptcha'),
        
        // CAPTCHA 표시 모드 (always: 항상, conditional: 조건부, disabled: 비활성화)
        'mode' => env('ADMIN_CAPTCHA_MODE', 'conditional'),
        
        // 조건부 모드에서 CAPTCHA를 표시할 실패 시도 횟수
        'show_after_attempts' => env('ADMIN_CAPTCHA_SHOW_AFTER_ATTEMPTS', 3),
        
        // 캐시 TTL (초 단위)
        'cache_ttl' => env('ADMIN_CAPTCHA_CACHE_TTL', 3600),
        
        // Google reCAPTCHA 설정
        'recaptcha' => [
            'site_key' => env('RECAPTCHA_SITE_KEY', ''),
            'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
            'version' => env('RECAPTCHA_VERSION', 'v2'),
            'threshold' => env('RECAPTCHA_THRESHOLD', 0.5),
        ],
        
        // hCaptcha 설정
        'hcaptcha' => [
            'site_key' => env('HCAPTCHA_SITE_KEY', ''),
            'secret_key' => env('HCAPTCHA_SECRET_KEY', ''),
        ],
        
        // CAPTCHA 로그 설정
        'log' => [
            'enabled' => true,
            'failed_only' => false,
        ],
        
        // CAPTCHA 메시지
        'messages' => [
            'required' => 'CAPTCHA 인증이 필요합니다.',
            'failed' => 'CAPTCHA 인증에 실패했습니다. 다시 시도해주세요.',
            'expired' => 'CAPTCHA가 만료되었습니다. 페이지를 새로고침해주세요.',
            'not_configured' => 'CAPTCHA가 올바르게 설정되지 않았습니다.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist Settings
    |--------------------------------------------------------------------------
    |
    | IP 기반 접근 제어 설정
    |
    */
    'ip_whitelist' => [
        // IP 화이트리스트 기능 활성화 여부
        'enabled' => env('ADMIN_IP_WHITELIST_ENABLED', false),
        
        // IP 화이트리스트 모드 (strict: 차단, log_only: 로그만 기록)
        'mode' => env('ADMIN_IP_WHITELIST_MODE', 'strict'),
        
        // 신뢰할 수 있는 프록시 서버 목록
        'trusted_proxies' => env('ADMIN_TRUSTED_PROXIES', ''),
        
        // 기본 허용 IP 목록 (개발 환경용)
        'default_allowed' => [
            '127.0.0.1',    // IPv4 localhost
            '::1',          // IPv6 localhost
        ],
        
        // IP 접근 로그 보관 기간 (일)
        'log_retention_days' => env('ADMIN_IP_LOG_RETENTION_DAYS', 90),
        
        // IP 차단 임계값
        'rate_limit' => [
            'max_attempts' => env('ADMIN_IP_MAX_ATTEMPTS', 5),
            'decay_minutes' => env('ADMIN_IP_DECAY_MINUTES', 60),
            'block_duration' => env('ADMIN_IP_BLOCK_DURATION', 1440), // 24시간
        ],
        
        // 캐시 설정
        'cache' => [
            'ttl' => env('ADMIN_IP_CACHE_TTL', 300), // 5분
            'key' => 'admin_ip_whitelist',
        ],
        
        // 알림 설정
        'notifications' => [
            'enabled' => env('ADMIN_IP_NOTIFY_ENABLED', false),
            'email' => env('ADMIN_IP_NOTIFY_EMAIL', ''),
            'slack_webhook' => env('ADMIN_IP_NOTIFY_SLACK', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Other Admin Settings
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default' => 10,
        'options' => [10, 25, 50, 100],
    ],

    'datetime' => [
        'format' => 'Y-m-d H:i:s',
        'timezone' => 'Asia/Seoul',
    ],

    'upload' => [
        'max_file_size' => 10485760, // 10MB in bytes
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ],
];
