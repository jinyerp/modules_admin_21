{{--
    Admin Template Create Form Component
    
    템플릿 생성 폼 Livewire 컴포넌트입니다.
    
    기능:
    - 템플릿 기본 정보 입력 (제목, 설명, 활성화 상태)
    - 실시간 유효성 검사
    - 생성 성공시 목록 페이지로 리다이렉트
    
    참고: 헤더와 설정 버튼은 별도 컴포넌트(CreateHeaderWithSettings)로 분리됨
--}}
<div class="p-6">
    {{-- 생성 폼 --}}
    <form wire:submit="save">
        <div class="space-y-6">
            {{-- 활성화 상태 체크박스 --}}
            <div>
                <label for="enable" class="flex items-center">
                    <input wire:model="enable" type="checkbox" id="enable" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Enable this template</span>
                </label>
            </div>

            {{-- 제목 입력 필드 (필수) --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input wire:model="title" type="text" id="title" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-300 @enderror"
                       placeholder="Enter template title">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 설명 입력 필드 (선택) --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea wire:model="description" id="description" rows="4" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                          placeholder="Enter template description"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 폼 액션 버튼들 --}}
            <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                {{-- 취소 버튼 - 목록으로 돌아가기 --}}
                <a href="{{ route('admin2.templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                {{-- 생성 버튼 - 로딩 스피너 포함 --}}
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Create Template
                </button>
            </div>
        </div>
    </form>
</div>