<a href="#" class="flex items-center gap-x-4 px-6 py-3 text-sm/6 font-semibold text-white hover:bg-white/5">
    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" 
         alt="" 
         class="size-8 rounded-full bg-gray-800 outline -outline-offset-1 outline-white/10" />
    <span class="sr-only">Your profile</span>
    <span aria-hidden="true">{{ auth()->user()->name ?? 'Admin User' }}</span>
</a>