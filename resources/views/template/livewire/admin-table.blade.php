<div class="mt-6">
    <!-- 성공/오류 메시지 -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- 선택된 항목 정보 및 삭제 버튼 -->
    @if($selectedCount > 0)
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg flex justify-between items-center">
            <span class="text-sm text-blue-700">
                {{ $selectedCount }}개 항목이 선택되었습니다.
            </span>
            <button wire:click="requestDeleteSelected"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                선택 삭제
            </button>
        </div>
    @endif

    <!-- 테이블 -->
    @if(isset($jsonData['index']['tablePath']) && !empty($jsonData['index']['tablePath']))

        @includeIf($jsonData['index']['tablePath'])

    @else
        @include('jiny-admin::template.components.config-error', [
            'title' => '테이블 설정 오류',
            'config' => 'index.tablePath'
        ])
    @endif

    <!-- 페이지네이션 및 결과 정보 -->
    <div class="mt-4">
        <!-- 결과 정보 표시 -->
        <div class="text-sm text-gray-700 mb-4">
            총 <span class="font-semibold">{{ $rows->total() }}</span>개 중 
            <span class="font-semibold">{{ $rows->firstItem() ?? 0 }}</span>번째부터 
            <span class="font-semibold">{{ $rows->lastItem() ?? 0 }}</span>번째까지 표시
        </div>
        
        <!-- 페이지네이션 -->
        @if($rows->hasPages())
            <div class="mt-4">
                {{ $rows->links() }}
            </div>
        @endif
    </div>

    <!-- 페이지 로딩 시간 표시 -->
    @if(isset($loadTime))
        <div class="mt-4 text-center">
            <span class="text-xs text-gray-500">
                페이지 로딩 시간: {{ number_format($loadTime, 3) }}초
            </span>
        </div>
    @endif

</div>
