<div wire:ignore.self>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh-page', () => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Drawer -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-hidden"
         aria-labelledby="drawer-title" 
         role="dialog" 
         aria-modal="true"
         x-data="{ show: @entangle('isOpen').live }"
         x-show="show"
         x-cloak
         style="display: none;">
        
        <!-- Background overlay -->
        <div x-show="show" 
             x-transition:enter="ease-in-out duration-500"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="$wire.close()"></div>

        <!-- Drawer panel -->
        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div x-show="show"
                 x-transition:enter="transform transition ease-in-out duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="relative w-screen max-w-md">
                
                <div class="h-full flex flex-col bg-white shadow-xl">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-medium text-white" id="drawer-title">
                                {{ $settings['index']['settingsDrawer']['title'] ?? 'Table Settings' }}
                            </h2>
                            <button wire:click="close" 
                                    class="ml-3 text-blue-100 hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-blue-100">
                            {{ $settings['index']['settingsDrawer']['description'] ?? 'Customize table display options' }}
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="space-y-6">
                            <!-- Pagination Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Pagination</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label for="perPage" class="block text-sm font-medium text-gray-700">Items per page</label>
                                        <select wire:model="perPage" id="perPage" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Sorting Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Sorting</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label for="sortField" class="block text-sm font-medium text-gray-700">Default sort field</label>
                                        <select wire:model="sortField" id="sortField" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="id">ID</option>
                                            <option value="title">Title</option>
                                            <option value="enable">Status</option>
                                            <option value="created_at">Created Date</option>
                                            <option value="updated_at">Updated Date</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="sortDirection" class="block text-sm font-medium text-gray-700">Sort direction</label>
                                        <select wire:model="sortDirection" id="sortDirection" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="asc">Ascending</option>
                                            <option value="desc">Descending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Toggles -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Features</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input wire:model="enableSearch" type="checkbox" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Enable search</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="enableBulkActions" type="checkbox" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Enable bulk actions</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="enablePagination" type="checkbox" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Enable pagination</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="enableStatusToggle" type="checkbox" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Enable status toggle</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Visible Columns -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Visible Columns</h3>
                                <div class="space-y-2">
                                    @if(isset($settings['index']['table']['columns']))
                                        @foreach($settings['index']['table']['columns'] as $key => $column)
                                            @if(isset($column['label']))
                                                <label class="flex items-center">
                                                    <input wire:model="visibleColumns" type="checkbox" value="{{ $key }}"
                                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $column['label'] }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach(['checkbox' => 'Checkbox', 'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 'enable' => 'Status', 'created_at' => 'Created Date', 'actions' => 'Actions'] as $key => $label)
                                        <label class="flex items-center">
                                            <input wire:model="visibleColumns" type="checkbox" value="{{ $key }}"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between">
                            <button wire:click="resetToDefaults" type="button" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Reset to Defaults
                            </button>
                            <div class="space-x-3">
                                <button wire:click="close" type="button" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button wire:click="save" type="button" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>