{{-- 
    Admin User Log 상세 보기
--}}
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Log Entry #{{ $data->id }}
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            User activity log details
        </p>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">User ID</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($data->user_id)
                        <a href="{{ route('admin.users.show', $data->user_id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 hover:underline">
                            {{ $data->user_id }} (View Profile)
                        </a>
                    @else
                        N/A
                    @endif
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($data->user_id)
                        <a href="{{ route('admin.users.show', $data->user_id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 hover:underline">
                            {{ $data->email }}
                        </a>
                    @else
                        {{ $data->email }}
                    @endif
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Name</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($data->user_id && $data->name)
                        <a href="{{ route('admin.users.show', $data->user_id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 hover:underline">
                            {{ $data->name }}
                        </a>
                    @else
                        {{ $data->name ?? 'N/A' }}
                    @endif
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Action</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @php
                        $colors = [
                            'login' => 'green',
                            'logout' => 'blue',
                            'failed_login' => 'red',
                            'test_login' => 'yellow'
                        ];
                        $labels = [
                            'login' => '로그인',
                            'logout' => '로그아웃',
                            'failed_login' => '로그인 실패',
                            'test_login' => '테스트'
                        ];
                        $color = $colors[$data->action] ?? 'gray';
                        $label = $labels[$data->action] ?? $data->action;
                    @endphp
                    @if($color == 'green')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    @elseif($color == 'blue')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    @elseif($color == 'red')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    @elseif($color == 'yellow')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    @endif
                        {{ $label }}
                    </span>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $data->ip_address ?? 'N/A' }}
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <span class="break-all">{{ $data->user_agent ?? 'N/A' }}</span>
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Session ID</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $data->session_id ?? 'N/A' }}
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Logged At</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($data->logged_at)
                        {{ \Carbon\Carbon::parse($data->logged_at)->format('Y-m-d H:i:s') }}
                    @else
                        N/A
                    @endif
                </dd>
            </div>
            @if($data->details)
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Additional Details</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <pre class="bg-gray-100 p-2 rounded text-xs">{{ json_encode($data->details, JSON_PRETTY_PRINT) }}</pre>
                </dd>
            </div>
            @endif
        </dl>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <a href="{{ route('admin.user.logs') }}" 
           class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
            Back to List
        </a>
    </div>
</div>