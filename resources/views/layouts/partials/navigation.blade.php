<ul role="list" class="-mx-2 space-y-1">
    <li>
        <a href="{{ route('admin.dashboard', ['prefix'=>request()->route('prefix') ?? 'admin']) }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users', ['prefix'=>request()->route('prefix') ?? 'admin']) }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.users*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Users
        </a>
    </li>
    <li>
        <a href="{{ route('admin.modules', ['prefix'=>request()->route('prefix') ?? 'admin']) }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.modules*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Modules
        </a>
    </li>
    <li>
        <a href="{{ route('admin.settings', ['prefix'=>request()->route('prefix') ?? 'admin']) }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.settings*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Settings
        </a>
    </li>
    
    {{-- Security Section --}}
    <li>
        <div class="text-xs/6 font-semibold text-gray-400 mt-4">보안</div>
    </li>
    <li>
        <a href="{{ route('admin.security.ip-whitelist') }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.security.ip-whitelist*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            IP 화이트리스트
        </a>
    </li>
    <li>
        <a href="{{ route('admin.captcha.logs') }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.captcha.logs*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            CAPTCHA 로그
        </a>
    </li>
    
    {{-- Documents Section --}}
    <li>
        <div class="text-xs/6 font-semibold text-gray-400 mt-4">리소스</div>
    </li>
    <li>
        <a href="#" 
           class="group flex gap-x-3 rounded-md text-gray-400 hover:text-white hover:bg-white/5 p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Documents
        </a>
    </li>
    <li>
        <a href="{{ route('admin.reports', ['prefix'=>request()->route('prefix') ?? 'admin']) }}" 
           class="group flex gap-x-3 rounded-md {{ request()->routeIs('admin.reports*') ? 'bg-white/5 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} p-2 text-sm/6 font-semibold">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                <path d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Reports
        </a>
    </li>
</ul>