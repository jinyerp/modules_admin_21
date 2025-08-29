<div wire:ignore.self>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Delete Confirmation Modal -->
    @if($isOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         x-data="{ show: @entangle('isOpen').live }"
         x-show="show"
         x-cloak
         style="display: none;">
        
        <!-- Background overlay -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal panel -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                    Delete {{ ucfirst($itemType) }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this {{ $itemType }}? 
                                        @if($itemTitle)
                                            <strong class="text-gray-700">"{{ $itemTitle }}"</strong> will be permanently removed.
                                        @else
                                            This action cannot be undone.
                                        @endif
                                    </p>
                                    
                                    <!-- Verification Code Section -->
                                    <div class="mt-4 space-y-3">
                                        <p class="text-sm font-medium text-gray-700">
                                            To confirm deletion, please enter the following code:
                                        </p>
                                        
                                        <!-- Random Code Display with Copy Button -->
                                        <div class="relative">
                                            <div class="flex items-center justify-between p-3 bg-gray-50 border-2 border-gray-200 rounded-lg">
                                                <code class="text-xl font-mono font-bold text-red-600 tracking-wider select-all">{{ $randomCode }}</code>
                                                <button wire:click="copyCode" type="button"
                                                        class="ml-3 inline-flex items-center px-3 py-1.5 border shadow-sm text-sm font-medium rounded-md transition-all duration-200
                                                               @if($copied && $inputCode === $randomCode) 
                                                                   border-green-500 text-green-700 bg-green-50 hover:bg-green-100
                                                               @else 
                                                                   border-gray-300 text-gray-700 bg-white hover:bg-gray-50
                                                               @endif
                                                               focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-indigo-500">
                                                    @if($copied && $inputCode === $randomCode)
                                                        <svg class="h-4 w-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        Copied!
                                                    @else
                                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Copy
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Input Field -->
                                        <div>
                                            <input wire:model.live="inputCode" 
                                                   type="text" 
                                                   placeholder="Enter verification code"
                                                   class="block w-full px-4 py-2.5 text-base font-mono tracking-wider border-2 rounded-lg shadow-sm transition-all duration-200
                                                          @if($inputCode === $randomCode) 
                                                              border-green-500 bg-green-50 focus:border-green-600 focus:ring-green-500
                                                          @elseif($inputCode && $inputCode !== $randomCode) 
                                                              border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500
                                                          @else 
                                                              border-gray-300 focus:border-indigo-500 focus:ring-indigo-500
                                                          @endif"
                                                   @if($isDeleting) disabled @endif
                                                   autocomplete="off"
                                                   spellcheck="false">
                                            
                                            @if($inputCode && $inputCode !== $randomCode)
                                                <p class="mt-1.5 text-sm text-red-600 flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    Code does not match. Please try again.
                                                </p>
                                            @elseif($inputCode === $randomCode)
                                                <p class="mt-1.5 text-sm text-green-600 flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Code verified! You can now delete.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button wire:click="confirmDelete" 
                                type="button"
                                @if($inputCode !== $randomCode || $isDeleting) disabled @endif
                                class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto 
                                       @if($inputCode === $randomCode && !$isDeleting) 
                                           bg-red-600 hover:bg-red-500 
                                       @else 
                                           bg-gray-300 cursor-not-allowed 
                                       @endif">
                            @if($isDeleting)
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            @else
                                Delete
                            @endif
                        </button>
                        <button wire:click="close" 
                                type="button"
                                @if($isDeleting) disabled @endif
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto 
                                       @if($isDeleting) opacity-50 cursor-not-allowed @endif">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>