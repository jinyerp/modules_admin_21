<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCreate extends Component
{
    public $jsonData;
    public $form = [];
    
    public function mount($jsonData = null, $form = [])
    {
        $this->jsonData = $jsonData;
        
        // 기본값 설정
        if (!empty($form)) {
            foreach ($form as $key => $value) {
                // 체크박스 필드는 boolean으로 변환
                if ($key === 'enable') {
                    $this->form[$key] = (bool) $value;
                } else {
                    $this->form[$key] = $value;
                }
            }
        }
    }
    
    public function save()
    {
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        
        // 실제 테이블 컬럼 목록 (is_default 제외)
        $allowedColumns = ['enable', 'name', 'slug', 'description', 'category', 'version', 'author', 'settings'];
        
        // 저장할 데이터 준비
        $insertData = [];
        foreach ($this->form as $key => $value) {
            // 허용된 컬럼만 저장
            if (in_array($key, $allowedColumns)) {
                // 체크박스 필드 처리 (boolean을 0/1로 변환)
                if ($key === 'enable') {
                    $insertData[$key] = $value ? 1 : 0;
                } else {
                    $insertData[$key] = $value ?: null;
                }
            }
        }
        
        // 필수 필드 검증
        if (empty($insertData['name'])) {
            session()->flash('error', 'Name 필드는 필수입니다.');
            return;
        }
        
        // slug 자동 생성
        if (empty($insertData['slug'])) {
            $insertData['slug'] = Str::slug($insertData['name']);
        }
        
        // timestamps 추가
        $insertData['created_at'] = now();
        $insertData['updated_at'] = now();
        
        try {
            // 데이터베이스에 저장
            $id = DB::table($tableName)->insertGetId($insertData);
            
            // 성공 메시지
            session()->flash('success', '성공적으로 생성되었습니다.');
            
            // 목록 페이지로 리다이렉트
            $this->dispatch('redirect-with-replace', url: '/admin2/templates');
            
        } catch (\Exception $e) {
            session()->flash('error', '저장 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
    
    public function cancel()
    {
        // 목록 페이지로 리다이렉트
        $this->dispatch('redirect-with-replace', url: '/admin2/templates');
    }
    
    // 동적 필드 처리
    public function updatedFormName($value)
    {
        // slug 자동 생성
        if (empty($this->form['slug'])) {
            $this->form['slug'] = Str::slug($value);
        }
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.admin-create');
    }
}