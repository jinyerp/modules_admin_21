<div class="space-y-6">
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700">
            타입 코드
        </label>
        <input type="text" 
               wire:model="form.code" 
               id="code" 
               name="code"
               class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm sm:text-sm"
               placeholder="예: super, admin, staff"
               maxlength="50"
               readonly
               disabled>
        <p class="mt-1 text-sm text-gray-500">타입 코드는 수정할 수 없습니다</p>
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">
            등급명 <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               wire:model="form.name" 
               id="name" 
               name="name"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
               placeholder="예: Super Admin"
               required>
        @error('form.name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">
            설명
        </label>
        <textarea wire:model="form.description" 
                  id="description" 
                  name="description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="이 등급에 대한 설명을 입력하세요"></textarea>
        @error('form.description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="level" class="block text-sm font-medium text-gray-700">
            권한 레벨 <span class="text-red-500">*</span>
        </label>
        <input type="number" 
               wire:model="form.level" 
               id="level" 
               name="level"
               min="0"
               max="100"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
               placeholder="0-100"
               required>
        <p class="mt-1 text-sm text-gray-500">0-100 사이의 값 (높을수록 높은 권한)</p>
        @error('form.level')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center">
        <input type="checkbox" 
               wire:model="form.enable" 
               id="enable" 
               name="enable"
               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
        <label for="enable" class="ml-2 block text-sm text-gray-900">
            활성화
        </label>
    </div>

    <div>
        <label for="pos" class="block text-sm font-medium text-gray-700">
            정렬 순서
        </label>
        <input type="number" 
               wire:model="form.pos" 
               id="pos" 
               name="pos"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('form.pos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>