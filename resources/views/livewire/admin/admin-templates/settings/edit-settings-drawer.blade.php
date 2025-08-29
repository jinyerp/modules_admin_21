<div wire:ignore.self>
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
                    <div class="px-6 py-4 bg-purple-700">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-medium text-white" id="drawer-title">
                                Edit Form Settings
                            </h2>
                            <button wire:click="close" 
                                    class="ml-3 text-purple-200 hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-purple-200">
                            Customize edit form options
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="space-y-6">
                            <!-- Form Layout -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Form Layout</h3>
                                <div>
                                    <label for="formLayout" class="block text-sm font-medium text-gray-700">Layout style</label>
                                    <select wire:model="formLayout" id="formLayout" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                        <option value="vertical">Vertical</option>
                                        <option value="horizontal">Horizontal</option>
                                        <option value="inline">Inline</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Action Buttons</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input wire:model="enableDelete" type="checkbox" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Show Delete button</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="enableListButton" type="checkbox" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Show Back to List button</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="enableDetailButton" type="checkbox" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Show View Details button</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Advanced Features -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Advanced Features</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input wire:model="trackChanges" type="checkbox" 
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Track changes (highlight modified fields)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Visible Sections -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Form Sections</h3>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input wire:model="visibleSections" type="checkbox" value="basic"
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Basic Information</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="visibleSections" type="checkbox" value="settings"
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Settings</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="visibleSections" type="checkbox" value="metadata"
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Metadata (Read-only)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Field Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Field Settings</h3>
                                <div class="space-y-3">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Required Fields</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            @foreach($requiredFields as $field)
                                            <li class="flex items-center">
                                                <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ ucfirst($field) }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Read-only Fields</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            @foreach($readonlyFields as $field)
                                            <li class="flex items-center">
                                                <svg class="h-4 w-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between">
                            <button wire:click="resetToDefaults" type="button" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Reset to Defaults
                            </button>
                            <div class="space-x-3">
                                <button wire:click="close" type="button" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Cancel
                                </button>
                                <button wire:click="save" type="button" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
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