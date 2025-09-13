{{-- 
    Email Templates 테이블 뷰
    Tailwind CSS 스타일 적용 및 Livewire 기능 통합
--}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-3 py-2 text-left">
                <input type="checkbox" 
                       wire:model.live="selectedAll"
                       class="h-3.5 w-3.5 text-blue-600 border-gray-200 rounded focus:ring-1 focus:ring-blue-500">
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                ID
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Name
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Slug
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Subject
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Type
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Status
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Created
            </th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($rows as $item)
        <tr class="hover:bg-gray-50">
            <td class="px-3 py-2.5 whitespace-nowrap">
                <input type="checkbox" 
                       wire:model.live="selected"
                       value="{{ $item->id }}"
                       class="h-3.5 w-3.5 text-blue-600 border-gray-200 rounded focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-gray-900">
                {{ $item->id }}
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap">
                @php
                    $showRoute = '/admin/emailtemplates/' . $item->id;
                @endphp
                <a href="{{ $showRoute }}" 
                   class="text-blue-600 hover:text-blue-900 text-xs">
                    {{ $item->name ?? '' }}
                </a>
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-gray-600">
                {{ $item->slug ?? '' }}
            </td>
            <td class="px-3 py-2.5 text-xs text-gray-600">
                {{ Str::limit($item->subject ?? '', 40) }}
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap">
                @if($item->type === 'markdown')
                    <span class="px-1.5 inline-flex text-xs leading-4 font-medium rounded-full bg-purple-100 text-purple-800">
                        Markdown
                    </span>
                @else
                    <span class="px-1.5 inline-flex text-xs leading-4 font-medium rounded-full bg-blue-100 text-blue-800">
                        HTML
                    </span>
                @endif
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap">
                @if($item->is_active)
                    <span class="px-1.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                        Active
                    </span>
                @else
                    <span class="px-1.5 inline-flex text-xs leading-4 font-medium rounded-full bg-gray-100 text-gray-800">
                        Inactive
                    </span>
                @endif
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-gray-500">
                @if($item->created_at)
                    {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}
                @else
                    -
                @endif
            </td>
            <td class="px-3 py-2.5 whitespace-nowrap text-xs">
                <div class="flex items-center space-x-1">
                    @php
                        $viewRoute = '/admin/emailtemplates/' . $item->id;
                        $editRoute = '/admin/emailtemplates/' . $item->id . '/edit';
                    @endphp
                    
                    {{-- Preview Button --}}
                    <button wire:click="preview({{ $item->id }})"
                            class="text-gray-600 hover:text-gray-900"
                            title="Preview">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    
                    {{-- Test Send Button --}}
                    <button wire:click="testSend({{ $item->id }})"
                            class="text-green-600 hover:text-green-900"
                            title="Test Send">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    
                    {{-- Edit Button --}}
                    <a href="{{ $editRoute }}" 
                       class="text-blue-600 hover:text-blue-900"
                       title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    
                    {{-- Delete Button --}}
                    <button wire:click="requestDeleteSingle({{ $item->id }})"
                            class="text-red-600 hover:text-red-900"
                            title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="px-3 py-4 text-center text-xs text-gray-500">
                No email templates found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>