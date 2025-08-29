<!DOCTYPE html>
<html class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jiny Admin Dashboard</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Plus Elements CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    
    <!-- Tailwind Plus Elements 초기화 -->
    <script type="module">
        import { defineCustomElements } from 'https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1/dist/index.js';
        defineCustomElements();
    </script>
    
    <!-- Livewire 3 CDN -->
    @livewireStyles
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- 추가 아이콘 및 폰트 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- 커스텀 스타일 -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="h-full">
    <!-- 모바일 사이드바 다이얼로그 -->
    <el-dialog>
        <dialog id="sidebar" class="backdrop:bg-transparent lg:hidden">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-900/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"></el-dialog-backdrop>

            <div tabindex="0" class="fixed inset-0 flex focus:outline-none">
                <el-dialog-panel class="group/dialog-panel relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full">
                    <div class="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out group-data-closed/dialog-panel:opacity-0">
                        <button type="button" command="close" commandfor="sidebar" class="-m-2.5 p-2.5">
                            <span class="sr-only">Close sidebar</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- 모바일 사이드바 컴포넌트 -->
                    @include('jiny-admin2::layouts.partials.sidebar')
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>

    <!-- 데스크톱용 정적 사이드바 -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col dark:bg-gray-900">
        <!-- 사이드바 컴포넌트 -->
        @include('jiny-admin2::layouts.partials.sidebar')
    </div>

    <!-- 모바일 상단 헤더 -->
    <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-xs sm:px-6 lg:hidden dark:bg-gray-900 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-0 dark:after:border-b dark:after:border-white/10 dark:after:bg-black/10">
        <button type="button" command="show-modal" commandfor="sidebar" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white">
            <span class="sr-only">Open sidebar</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="flex-1 text-sm/6 font-semibold text-gray-900 dark:text-white">사용자 관리</div>
        <a href="#">
            <span class="sr-only">Your profile</span>
            <div class="size-8 rounded-full bg-indigo-600 flex items-center justify-center">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
        </a>
    </div>

    <!-- 메인 콘텐츠 영역 -->
    <main class="py-10 lg:pl-72">
        <div class="px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Livewire 3 스크립트 -->
    @livewireScripts
    
    <!-- Livewire Modal Support -->
    @stack('modals')
</body>
</html>
