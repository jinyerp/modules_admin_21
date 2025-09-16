# Jiny Admin 패키지 설치 가이드

## 자동 설치 (권장)

패키지 설치 후 다음 명령어를 실행하세요:

```bash
php artisan admin:install
```

이 명령어는 다음 작업을 자동으로 수행합니다:
- Tailwind CSS 설정 업데이트 (v3, v4 자동 감지)
- 설정 파일 발행
- 데이터베이스 마이그레이션
- 관리자 계정 생성

## 수동 설치

### 1. Tailwind CSS 설정

#### Tailwind CSS v4 (app.css 사용)
`resources/css/app.css` 파일에 다음 라인을 추가하세요:

```css
@source '../../vendor/jinyerp/**/*.blade.php';
@source '../../vendor/jinyerp/**/*.php';
@source '../../vendor/jiny/**/*.blade.php';
@source '../../vendor/jiny/**/*.php';
```

#### Tailwind CSS v3 (tailwind.config.js 사용)
`tailwind.config.js` 파일의 content 배열에 다음 경로를 추가하세요:

```javascript
export default {
    content: [
        // ... 기존 경로들
        './vendor/jinyerp/**/*.blade.php',
        './vendor/jiny/**/*.blade.php',
    ],
}
```

### 2. 설정 파일 발행

```bash
php artisan vendor:publish --tag=jiny-admin-config
```

### 3. 마이그레이션 실행

```bash
php artisan migrate
```

### 4. 관리자 계정 생성

```bash
php artisan admin:user-create
```

### 5. 빌드 및 실행

```bash
# CSS 빌드
npm run build

# 개발 서버 실행
php artisan serve
npm run dev  # 별도 터미널에서
```

## 설치 확인

브라우저에서 `http://localhost:8000/admin` 접속하여 로그인 페이지가 정상적으로 표시되는지 확인하세요.

## 문제 해결

### Tailwind CSS가 적용되지 않는 경우

1. `npm run build` 명령어를 다시 실행하세요
2. 브라우저 캐시를 삭제하세요
3. `resources/css/app.css` 파일에 vendor 경로가 포함되어 있는지 확인하세요

### 라우트 오류가 발생하는 경우

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```