<!-- 사이드바 컴포넌트 -->
<div class="relative flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-gray-900 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10" x-data="{ 
    usersOpen: false, 
    contentOpen: false 
}">
    <!-- 로고 영역 -->
    <div class="relative flex h-16 shrink-0 items-center">
        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Jiny Admin" class="h-8 w-auto dark:hidden" />
        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Jiny Admin" class="h-8 w-auto not-dark:hidden" />
    </div>
    
    <!-- 네비게이션 메뉴 -->
    <nav class="relative flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- 대시보드 -->
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md bg-gray-50 p-2 text-sm/6 font-semibold text-gray-700 dark:bg-white/5 dark:text-gray-400 dark:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            대시보드
                        </a>
                    </li>
                    
                    <!-- 사용자 관리 (Dropdown) -->
                    <li>
                        <button @click="usersOpen = !usersOpen" class="flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            사용자 관리
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-auto size-5 shrink-0 transition-transform duration-200" :class="usersOpen ? 'rotate-90 text-gray-500 dark:text-gray-400' : 'text-gray-400'">
                                <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="usersOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-1 px-2">
                            <ul>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">사용자 목록</a>
                                </li>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">역할 관리</a>
                                </li>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">권한 설정</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <!-- 콘텐츠 관리 (Dropdown) -->
                    <li>
                        <button @click="contentOpen = !contentOpen" class="flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            콘텐츠 관리
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-auto size-5 shrink-0 transition-transform duration-200" :class="contentOpen ? 'rotate-90 text-gray-500 dark:text-gray-400' : 'text-gray-400'">
                                <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="contentOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-1 px-2">
                            <ul>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">게시물 관리</a>
                                </li>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">카테고리 관리</a>
                                </li>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">태그 관리</a>
                                </li>
                                <li>
                                    <a href="#" class="block rounded-md py-2 pr-2 pl-9 text-sm/6 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">댓글 관리</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <!-- 일정 관리 -->
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            일정 관리
                        </a>
                    </li>
                    
                    <!-- 문서 관리 -->
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            문서 관리
                        </a>
                    </li>
                    
                    <!-- 리포트 -->
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0 text-gray-400 dark:text-current">
                                <path d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            리포트
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- 팀 관리 섹션 -->
            <li>
                <div class="text-xs/6 font-semibold text-gray-400">팀 관리</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium text-gray-400 group-hover:border-indigo-600 group-hover:text-indigo-600 dark:border-white/10 dark:bg-white/5 dark:group-hover:border-white/20 dark:group-hover:text-white">A</span>
                            <span class="truncate">관리자 팀</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium text-gray-400 group-hover:border-indigo-600 group-hover:text-indigo-600 dark:border-white/10 dark:bg-white/5 dark:group-hover:border-white/20 dark:group-hover:text-white">U</span>
                            <span class="truncate">사용자 팀</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        
        <!-- 하단 사용자 프로필 -->
        <li class="-mx-6 mt-auto">
            <a href="#" class="flex items-center gap-x-4 px-6 py-3 text-sm/6 font-semibold text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5 dark:bg-gray-800 dark:outline-white/10" />
                <span class="sr-only">Your profile</span>
                <span aria-hidden="true">관리자</span>
            </a>
        </li>
    </nav>
</div>
