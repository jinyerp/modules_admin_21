<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jiny\Admin\Http\Controllers\AdminController;

/**
 * AdminUsers Delete Controller
 * 
 * 관리자 회원 삭제 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminUsersDelete extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        // 테이블 설정
        $this->actions['table']['name'] = "users";
        
        // 페이지 제목 설정
        $this->actions['title'] = "Delete Admin User";
        $this->actions['subtitle'] = "Remove administrator account";

        // 삭제 후 리다이렉트 경로
        $this->actions['redirect'] = "/admin/users";
    }

    /**
     * Single Action __invoke method
     * 단일 삭제 또는 다중 삭제 처리
     */
    public function __invoke(Request $request, $id = null)
    {
        // 다중 선택 삭제
        if ($request->has('selected')) {
            return $this->destroyMultiple($request);
        }
        
        // 단일 삭제
        if ($id) {
            return $this->destroy($request, $id);
        }
        
        return redirect($this->actions['redirect'])
            ->with('error', 'No user selected for deletion');
    }

    /**
     * 단일 데이터 삭제
     */
    public function destroy(Request $request, $id)
    {
        // 삭제할 데이터 조회
        $row = DB::table($this->actions['table']['name'])
            ->where('id', $id)
            ->first();
        
        if (!$row) {
            return redirect($this->actions['redirect'])
                ->with('error', 'User not found');
        }
        
        $row = (array) $row;
        
        // 삭제 전 훅 실행
        $result = $this->hookDeleting(null, $row);
        
        if ($result === false) {
            return redirect($this->actions['redirect'])
                ->with('error', session('error', 'Cannot delete this user'));
        }
        
        // 데이터베이스에서 삭제
        DB::table($this->actions['table']['name'])
            ->where('id', $id)
            ->delete();
        
        // 삭제 후 훅 실행
        $this->hookDeleted(null, $row);
        
        return redirect($this->actions['redirect'])
            ->with('success', 'Admin user deleted successfully');
    }

    /**
     * 다중 데이터 삭제
     */
    public function destroyMultiple(Request $request)
    {
        $selected = $request->get('selected', []);
        
        if (empty($selected)) {
            return redirect($this->actions['redirect'])
                ->with('error', 'No users selected for deletion');
        }
        
        // 선택 삭제 전 훅 실행
        $this->hookCheckDeleting(null, $selected);
        
        $deletedCount = 0;
        
        foreach ($selected as $id) {
            $row = DB::table($this->actions['table']['name'])
                ->where('id', $id)
                ->first();
            
            if ($row) {
                $row = (array) $row;
                
                // 각 항목에 대해 삭제 전 훅 실행
                $result = $this->hookDeleting(null, $row);
                
                if ($result !== false) {
                    // 데이터베이스에서 삭제
                    DB::table($this->actions['table']['name'])
                        ->where('id', $id)
                        ->delete();
                    
                    // 삭제 후 훅 실행
                    $this->hookDeleted(null, $row);
                    
                    $deletedCount++;
                }
            }
        }
        
        // 선택 삭제 후 훅 실행
        $this->hookCheckDeleted(null, $selected);
        
        return redirect($this->actions['redirect'])
            ->with('success', "$deletedCount admin user(s) deleted successfully");
    }

    /**
     * delete 동작이 실행되기 전 호출됩니다.
     */
    public function hookDeleting($wire, array $row)
    {
        // 자기 자신은 삭제할 수 없음
        if (Auth::check() && Auth::id() == $row['id']) {
            session()->flash('error', 'You cannot delete your own account.');
            return false;
        }

        return $row;
    }

    /**
     * delete 동작이 실행된 후 호출됩니다.
     */
    public function hookDeleted($wire, $row)
    {
        $userId = $row['id'];

        // 관리자 권한 제거
        DB::table('users_admin')->where('user_id', $userId)->delete();
        DB::table('users_super')->where('user_id', $userId)->delete();

        return $row;
    }

    /**
     * 선택해서 삭제하는 경우 호출됩니다.
     */
    public function hookCheckDeleting($wire, $selected)
    {
        // 선택 삭제 전 추가 작업이 필요한 경우 여기에 구현
    }

    /**
     * 선택해서 삭제하기 후에 호출됩니다.
     */
    public function hookCheckDeleted($wire, $selected)
    {
        // 선택 삭제 후 추가 작업이 필요한 경우 여기에 구현
    }
}