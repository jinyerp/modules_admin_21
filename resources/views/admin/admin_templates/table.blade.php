<article class="mt-8 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    {{-- Checkbox --}}
                    <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                        <div class="flex h-6 items-center">
                            <input
                                type="checkbox"
                                wire:model.live="selectedAll"
                                class="h-4 w-4 rounded border-0 ring-0 outline-none bg-white text-indigo-600 focus:ring-0 focus:ring-offset-0 focus:outline-none checked:border-0 checked:ring-0 hover:border-0 hover:ring-0 dark:bg-gray-800"
                            />
                        </div>
                    </th>

                            {{-- ID --}}
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('id')" class="group inline-flex items-center">
                                    ID
                                    @if($sortField === 'id')
                                        @if($sortDirection === 'asc')
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="ml-1 h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            {{-- Name --}}
                            <th scope="col" class="min-w-48 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('name')" class="group inline-flex items-center">
                                    Name
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="ml-1 h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            {{-- Slug --}}
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('slug')" class="group inline-flex items-center">
                                    Slug
                                    @if($sortField === 'slug')
                                        @if($sortDirection === 'asc')
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="ml-1 h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            {{-- Category --}}
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                Category
                            </th>

                            {{-- Status --}}
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                Status
                            </th>

                            {{-- Created --}}
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                <button wire:click="sortBy('created_at')" class="group inline-flex items-center">
                                    Created
                                    @if($sortField === 'created_at')
                                        @if($sortDirection === 'asc')
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="ml-1 h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            {{-- Actions --}}
                            <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ in_array($row->id, $selected) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                {{-- Checkbox --}}
                                <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    @if(in_array($row->id, $selected))
                                        <div class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600 dark:bg-indigo-500"></div>
                                    @endif
                                    <div class="flex h-5 items-center">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selected"
                                            value="{{ $row->id }}"
                                            class="h-4 w-4 rounded border-0 ring-0 outline-none bg-white text-indigo-600 focus:ring-0 focus:ring-offset-0 focus:outline-none checked:border-0 checked:ring-0 hover:border-0 hover:ring-0 dark:bg-gray-800"
                                        />
                                    </div>
                                </td>

                                {{-- ID --}}
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $row->id }}
                                </td>

                                {{-- Name --}}
                                <td class="py-4 px-3 text-sm font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                    <a href="/admin2/templates/{{ $row->id }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $row->name ?? '-' }}
                                    </a>
                                </td>

                                {{-- Slug --}}
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ $row->slug ?? '-' }}
                                </td>

                                {{-- Category --}}
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    @if($row->category)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                            {{ $row->category }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-4 text-sm whitespace-nowrap">
                                    @if($row->enable)
                                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                            Enabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                            Disabled
                                        </span>
                                    @endif
                                </td>

                                {{-- Created --}}
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-3">
                                    <a href="/admin2/templates/{{ $row->id }}/edit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Edit<span class="sr-only">, {{ $row->name }}</span>
                                    </a>
                                    <button
                                        wire:click="requestDeleteSingle({{ $row->id }})"
                                        class="ml-3 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Delete<span class="sr-only">, {{ $row->name }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">템플릿이 없습니다.</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">새 템플릿을 추가하여 시작하세요.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
    </div>
</article>
