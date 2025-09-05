{{-- 
    AdminTest 수정 폼 필드
    admin-edit.blade.php에서 include되어 사용됨
    폼 필드만 포함 (버튼은 admin-edit.blade.php에서 처리)
--}}

<div class="grid grid-cols-6 gap-6">
    {{-- 제목 --}}
    <div class="col-span-6 sm:col-span-4">
        <label for="title" class="block text-sm font-medium text-gray-700">
            제목 <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               wire:model="form.title"
               id="title" 
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
               required>
        @error('form.title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 설명 --}}
    <div class="col-span-6">
        <label for="description" class="block text-sm font-medium text-gray-700">
            설명
        </label>
        <textarea wire:model="form.description"
                  id="description" 
                  rows="3"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
        @error('form.description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 활성화 상태 --}}
    <div class="col-span-6 sm:col-span-3">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox" 
                       wire:model="form.enable"
                       id="enable"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="enable" class="font-medium text-gray-700">활성화</label>
                <p class="text-gray-500">이 항목을 활성화합니다.</p>
            </div>
        </div>
    </div>
    
    {{-- 순서 --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="pos" class="block text-sm font-medium text-gray-700">
            순서
        </label>
        <input type="number" 
               wire:model="form.pos"
               id="pos"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.pos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 깊이 --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="depth" class="block text-sm font-medium text-gray-700">
            깊이
        </label>
        <input type="number" 
               wire:model="form.depth"
               id="depth"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.depth')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 참조 --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="ref" class="block text-sm font-medium text-gray-700">
            참조
        </label>
        <input type="number" 
               wire:model="form.ref"
               id="ref"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.ref')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>