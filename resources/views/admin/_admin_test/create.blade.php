{{-- 
    ============================================================================
    AdminTest 생성 폼 필드 (Form Fields Only)
    ============================================================================
    
    ⚠️ 중요: 이 파일은 폼 필드만 포함해야 합니다!
    
    이 파일은 Livewire 컴포넌트 AdminCreate와 함께 사용되며,
    /template/livewire/admin-create.blade.php 에서 include되어 사용됩니다.
    
    ❌ 포함하지 말아야 할 것들:
    - <form> 태그 (admin-create.blade.php에서 제공)
    - 제출 버튼 (저장, 취소 등 - admin-create.blade.php에서 제공)
    - 전체 페이지 컨테이너 <div> (불필요한 중첩 방지)
    - 페이지 제목이나 설명 (admin-header-with-settings에서 제공)
    
    ✅ 포함해야 할 것들:
    - 입력 필드들 (input, textarea, select, checkbox 등)
    - 필드 레이블 (label)
    - 유효성 검사 에러 메시지 (@error 디렉티브)
    - 필드 그룹핑을 위한 grid 레이아웃
    
    📝 Livewire 바인딩:
    - wire:model="form.필드명" 형식으로 바인딩
    - AdminCreate 컴포넌트의 $form 속성과 연결됨
    
    🎨 스타일링:
    - Tailwind CSS 클래스 사용
    - Bootstrap 클래스 사용 금지 (일관성 유지)
    
    🔄 마이그레이션과 동기화:
    - 데이터베이스 마이그레이션 파일의 컬럼과 일치해야 함
    - 기본 컬럼: title, description, enable, pos, depth, ref
    - 추가 컬럼이 있다면 해당 입력 필드 추가 필요
    
    📌 파일 경로 구조:
    - 이 파일: /resources/views/admin/admin_test/create.blade.php
    - 포함되는 곳: /resources/views/template/livewire/admin-create.blade.php
    - JSON 설정: create.formPath에 정의됨
    
    🚨 주의사항:
    admin:make 명령으로 재생성 시 이 주석을 참고하여
    폼 필드만 포함하도록 주의하세요!
    ============================================================================
--}}

<div class="grid grid-cols-6 gap-6">
    {{-- 
        제목 필드 (Title)
        - 필수 입력 필드 (required)
        - 최대 255자 제한 (데이터베이스 varchar)
    --}}
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
    
    {{-- 
        설명 필드 (Description)
        - 선택 입력 필드
        - text 타입 (긴 텍스트 가능)
    --}}
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
    
    {{-- 
        활성화 상태 (Enable)
        - boolean 타입 (true/false)
        - 기본값: true (활성화)
    --}}
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
    
    {{-- 
        순서 필드 (Position)
        - integer 타입
        - 기본값: 0
        - 정렬 순서를 결정하는 데 사용
    --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="pos" class="block text-sm font-medium text-gray-700">
            순서
        </label>
        <input type="number" 
               wire:model="form.pos"
               id="pos"
               value="0"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.pos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 
        깊이 필드 (Depth)
        - integer 타입
        - 기본값: 0
        - 계층 구조에서의 깊이를 나타냄
    --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="depth" class="block text-sm font-medium text-gray-700">
            깊이
        </label>
        <input type="number" 
               wire:model="form.depth"
               id="depth"
               value="0"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.depth')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 
        참조 필드 (Reference)
        - integer 타입
        - 기본값: 0
        - 부모 또는 관련 항목의 ID를 저장
    --}}
    <div class="col-span-6 sm:col-span-2">
        <label for="ref" class="block text-sm font-medium text-gray-700">
            참조
        </label>
        <input type="number" 
               wire:model="form.ref"
               id="ref"
               value="0"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        @error('form.ref')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    {{-- 
        ============================================================================
        추가 필드를 넣어야 할 경우:
        
        1. 마이그레이션 파일에 새 컬럼 추가
        2. 모델의 $fillable 배열에 필드명 추가
        3. 여기에 새 입력 필드 추가 (위 패턴 참고)
        4. AdminCreate 컴포넌트의 유효성 검사 규칙 확인
        
        예시:
        <div class="col-span-6 sm:col-span-3">
            <label for="custom_field" class="block text-sm font-medium text-gray-700">
                커스텀 필드
            </label>
            <input type="text" 
                   wire:model="form.custom_field"
                   id="custom_field"
                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            @error('form.custom_field')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        ============================================================================
    --}}
</div>