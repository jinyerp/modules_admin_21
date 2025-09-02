{{-- 
    Admin User Logs 테이블 뷰
    로그인/로그아웃 활동 로그 표시
--}}
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left">
                    <input type="checkbox" 
                           wire:model.live="selectedAll"
                           class="h-4 w-4 text-blue-600 border-gray-200 rounded focus:ring-1 focus:ring-blue-500">
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button wire:click="sortBy('id')" class="flex items-center">
                        ID
                        @if($sortField === 'id')
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z' : 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z' }}" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button wire:click="sortBy('action')" class="flex items-center">
                        Action
                        @if($sortField === 'action')
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z' : 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z' }}" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    IP Address
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button wire:click="sortBy('logged_at')" class="flex items-center">
                        Time
                        @if($sortField === 'logged_at')
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z' : 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z' }}" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($rows as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" 
                           wire:model.live="selected"
                           value="{{ $item->id }}"
                           class="h-4 w-4 text-blue-600 border-gray-200 rounded focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $item->id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($item->user_id)
                        <a href="{{ route('admin.users.show', $item->user_id) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-900 hover:underline">
                            {{ $item->email }}
                        </a>
                    @else
                        <span class="text-sm text-gray-900">{{ $item->email }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($item->user_id && $item->name)
                        <a href="{{ route('admin.users.show', $item->user_id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 hover:underline">
                            {{ $item->name }}
                        </a>
                    @else
                        {{ $item->name ?? '-' }}
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
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
                        $color = $colors[$item->action] ?? 'gray';
                        $label = $labels[$item->action] ?? $item->action;
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
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $item->ip_address ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($item->logged_at)
                        {{ \Carbon\Carbon::parse($item->logged_at)->format('Y-m-d H:i:s') }}
                    @elseif($item->created_at)
                        {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s') }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.user.logs.show', $item->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900">View</a>
                        <button wire:click="requestDeleteSingle({{ $item->id }})"
                                class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    로그 데이터가 없습니다.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>