<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin2 Module')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
{{--
    중앙 정렬 레이아웃 템플릿
    - Admin2 모듈의 기본 레이아웃
    - 화면 중앙에 최대 너비 md(28rem)의 콘텐츠 영역 제공
    - 다크 모드 지원 (bg-gray-100 dark:bg-gray-900)
    - 전체 화면 높이(min-h-screen)를 사용하여 수직 중앙 정렬
--}}
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center"
    data-page="@yield('script-state')">
    <div class="w-full max-w-md">

        @yield('content')

    </div>

    @stack('scripts')
</body>
</html>