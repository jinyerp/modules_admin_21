{{--
    이메일 발송 로그 생성 폼
    
    @package jiny/admin
    @subpackage admin_email_logs
    @description 새로운 이메일을 작성하고 발송하는 폼입니다.
                템플릿 선택, 받는사람/보내는사람 정보, 제목, 본문 등을 입력받습니다.
    @version 1.0
--}}

{{-- Email 발송 폼 --}}
<div class="space-y-6">
    {{-- 템플릿 선택 --}}
    @if($jsonData['create']['enableTemplateSelector'] ?? false)
    <div>
        <label for="template_id" class="block text-sm font-medium text-gray-700">템플릿 선택 (선택사항)</label>
        <select wire:model.live="form.template_id" wire:change="loadTemplate"
                id="template_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="">직접 작성</option>
            @if(isset($templates))
                @foreach($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
    @endif

    {{-- 받는 사람 정보 --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="to_email" class="block text-sm font-medium text-gray-700">
                받는 사람 이메일 <span class="text-red-500">*</span>
            </label>
            <input type="email" wire:model="form.to_email" id="to_email"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="example@email.com" required>
            @error('form.to_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="to_name" class="block text-sm font-medium text-gray-700">받는 사람 이름</label>
            <input type="text" wire:model="form.to_name" id="to_name"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="홍길동">
            @error('form.to_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- 보내는 사람 정보 --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="from_email" class="block text-sm font-medium text-gray-700">보내는 사람 이메일</label>
            <input type="email" wire:model="form.from_email" id="from_email"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="{{ config('mail.from.address') }}">
            @error('form.from_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="from_name" class="block text-sm font-medium text-gray-700">보내는 사람 이름</label>
            <input type="text" wire:model="form.from_name" id="from_name"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="{{ config('mail.from.name') }}">
            @error('form.from_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- 제목 --}}
    <div>
        <label for="subject" class="block text-sm font-medium text-gray-700">
            제목 <span class="text-red-500">*</span>
        </label>
        <input type="text" wire:model="form.subject" id="subject"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
               placeholder="이메일 제목을 입력하세요" required>
        @error('form.subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- 내용 --}}
    <div>
        <label for="body" class="block text-sm font-medium text-gray-700">
            내용 <span class="text-red-500">*</span>
        </label>
        <div wire:ignore>
            <textarea wire:model="form.body" id="body"
                      rows="10"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      placeholder="이메일 내용을 작성하세요" required></textarea>
        </div>
        @error('form.body') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    {{-- 상태 (숨김 - 자동으로 pending 설정) --}}
    <input type="hidden" wire:model="form.status" value="pending">

    {{-- 버튼 그룹 (Livewire 컴포넌트가 처리하므로 여기서는 제거) --}}
    {{-- 버튼은 AdminCreate 컴포넌트에서 자동으로 추가됩니다 --}}
</div>

@push('scripts')
<script>
    // TinyMCE 또는 다른 WYSIWYG 에디터 초기화 코드
    document.addEventListener('DOMContentLoaded', function() {
        // 에디터 초기화 (필요시)
    });
    
    // 템플릿 로드 시 폼 업데이트
    window.addEventListener('templateLoaded', event => {
        if (event.detail && event.detail.data) {
            // 템플릿 데이터로 폼 필드 업데이트
        }
    });
</script>
@endpush