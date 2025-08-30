<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * AdminTemplates Delete Controller
 * 
 * 관리자 템플릿 삭제 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminTemplatesDelete extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
        
        // 기본 리다이렉트 경로 설정 방식 변경 - 직접 route 정보를 사용
    }

    /**
     * __DIR__에서 AdminTemplates.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTemplates.json';
            
            if (!file_exists($jsonFilePath)) {
                return null;
            }

            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $jsonData;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Single Action __invoke method
     * 삭제 확인 및 처리
     */
    public function __invoke(Request $request, $id)
    {
        if ($request->isMethod('delete') || $request->isMethod('post')) {
            return $this->destroy($request, $id);
        }
        
        return $this->confirm($request, $id);
    }

    /**
     * 삭제 확인 화면 표시
     */
    public function confirm(Request $request, $id)
    {
        // 데이터베이스에서 템플릿 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $query = DB::table($tableName);
        
        // 기본 where 조건 적용
        if (isset($this->jsonData['table']['where']['default'])) {
            foreach ($this->jsonData['table']['where']['default'] as $condition) {
                if (count($condition) === 3) {
                    $query->where($condition[0], $condition[1], $condition[2]);
                } elseif (count($condition) === 2) {
                    $query->where($condition[0], $condition[1]);
                }
            }
        }
        
        $template = $query->where('id', $id)->first();
        
        if (!$template) {
            return redirect($this->getRedirectUrl())
                ->with('error', '템플릿을 찾을 수 없습니다.');
        }
        
        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }
        
        // template.delete view 경로 확인 (delete 템플릿이 없을 수도 있음)
        // delete는 선택적이므로 없으면 기본값 사용
        $viewPath = $this->jsonData['template']['delete'] ?? 
                    'jiny-admin2::admin.admin_templates.delete';
        
        return view($viewPath, [
            'jsonData' => $this->jsonData,
            'template' => $template,
            'id' => $id,
            'confirmMessage' => $this->jsonData['delete']['confirmation']['message'] ?? 
                              $this->jsonData['destroy']['confirmation']['message'] ?? 
                              'Are you sure you want to delete this template?',
            'requireConfirmation' => $this->jsonData['delete']['requireConfirmation'] ?? 
                                      $this->jsonData['delete']['features']['requireConfirmation'] ?? true
        ]);
    }

    /**
     * 데이터 삭제
     */
    public function destroy(Request $request, $id)
    {
        // 삭제 확인 검증
        $requireConfirmation = $this->jsonData['delete']['requireConfirmation'] ?? 
                              $this->jsonData['destroy']['requireConfirmation'] ?? 
                              $this->jsonData['delete']['features']['requireConfirmation'] ?? 
                              $this->jsonData['destroy']['features']['requireConfirmation'] ?? true;
        
        if ($requireConfirmation && !$request->has('confirm_delete')) {
            return redirect()->back()
                ->with('error', $this->jsonData['destroy']['messages']['confirmRequired'] ?? 
                              $this->jsonData['messages']['destroy']['confirmRequired'] ?? 
                              'Delete confirmation required.');
        }
        
        // 삭제할 템플릿 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $template = DB::table($tableName)
            ->where('id', $id)
            ->first();
        
        if (!$template) {
            return redirect($this->getRedirectUrl())
                ->with('error', '템플릿을 찾을 수 없습니다.');
        }
        
        // 삭제 전 훅 실행
        $canDelete = $this->hookDeleting(null, $template);
        
        if ($canDelete === false) {
            return redirect($this->getRedirectUrl())
                ->with('error', '이 템플릿은 삭제할 수 없습니다.');
        }
        
        // 트랜잭션 처리
        $enableTransaction = $this->jsonData['delete']['enableTransaction'] ?? 
                           $this->jsonData['destroy']['enableTransaction'] ?? 
                           $this->jsonData['delete']['features']['enableTransaction'] ?? 
                           $this->jsonData['destroy']['features']['enableTransaction'] ?? true;
        
        if ($enableTransaction) {
            DB::beginTransaction();
        }
        
        try {
            // 데이터베이스에서 삭제
            DB::table($tableName)
                ->where('id', $id)
                ->delete();
            
            // 로깅 처리
            $this->logDeletion($template);
            
            // 삭제 후 훅 실행
            $this->hookDeleted(null, $template);
            
            if ($enableTransaction) {
                DB::commit();
            }
            
            // 성공 메시지와 함께 목록으로 리다이렉트
            $message = $this->jsonData['destroy']['messages']['success'] ?? 
                      $this->jsonData['messages']['destroy']['success'] ?? 
                      '템플릿이 성공적으로 삭제되었습니다.';
            
            return redirect($this->getRedirectUrl())
                ->with('success', $message);
            
        } catch (\Exception $e) {
            if ($enableTransaction) {
                DB::rollBack();
            }
            
            $errorMessage = sprintf(
                $this->jsonData['destroy']['messages']['error'] ?? 
                $this->jsonData['messages']['destroy']['error'] ?? 
                'Error deleting template: %s',
                $e->getMessage()
            );
            
            return redirect($this->getRedirectUrl())
                ->with('error', $errorMessage);
        }
    }

    /**
     * 데이터 삭제 전에 호출됩니다.
     * false를 반환하면 삭제가 취소됩니다.
     */
    public function hookDeleting($wire, $template)
    {
        // 기본 템플릿인지 확인
        if ($template->is_default ?? false) {
            return false; // 기본 템플릿은 삭제 불가
        }
        
        // 사용 중인 템플릿인지 확인할 수 있음
        // 예: 다른 테이블에서 이 템플릿을 참조하는지 체크
        
        return true;
    }

    /**
     * 데이터 삭제 후에 호출됩니다.
     */
    public function hookDeleted($wire, $template)
    {
        // 필요시 추가 작업 수행
        // 예: 관련 파일 삭제, 캐시 클리어 등
        
        return $template;
    }
    
    /**
     * 삭제 로그 기록
     */
    private function logDeletion($template)
    {
        $loggingConfig = $this->jsonData['delete']['logging'] ?? 
                        $this->jsonData['destroy']['logging'] ?? [];
        
        if (!($loggingConfig['enabled'] ?? false)) {
            return;
        }
        
        $channel = $loggingConfig['channel'] ?? 'admin';
        $level = $loggingConfig['level'] ?? 'info';
        
        $logData = [
            'action' => 'delete_template',
            'template_id' => $template->id,
            'template_name' => $template->name ?? $template->title ?? 'Unknown',
        ];
        
        if ($loggingConfig['includeUser'] ?? true) {
            $logData['user_id'] = Auth::id();
            $logData['user_email'] = Auth::user()->email ?? null;
        }
        
        if ($loggingConfig['includeIp'] ?? true) {
            $logData['ip_address'] = request()->ip();
        }
        
        Log::channel($channel)->$level('Template deleted', $logData);
    }
    
    /**
     * 리다이렉트 URL 가져오기
     */
    private function getRedirectUrl()
    {
        if (isset($this->jsonData['route']['name'])) {
            return route($this->jsonData['route']['name'] . '.index');
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            return route($this->jsonData['route'] . '.index');
        }
        return '/admin2/templates';
    }
}