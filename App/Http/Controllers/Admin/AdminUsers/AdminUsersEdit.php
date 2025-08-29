<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jiny\Admin\Http\Controllers\AdminController;

/**
 * AdminUsers Edit Controller
 * 
 * 관리자 회원 수정 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminUsersEdit extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        // 테이블 설정
        $this->actions['table']['name'] = "users";
        
        // 뷰 파일 설정
        $this->actions['view']['form'] = "jiny-admin2::admin.admin_users.form";
        $this->actions['view']['edit'] = "jiny-admin2::admin.admin_users.edit";

        // 페이지 제목 설정
        $this->actions['title'] = "Edit Admin User";
        $this->actions['subtitle'] = "Update administrator account information";

        // 수정 후 리다이렉트 경로
        $this->actions['redirect'] = "/admin/users";
    }

    /**
     * Single Action __invoke method
     * 수정 폼 및 업데이트 처리
     */
    public function __invoke(Request $request, $id)
    {
        if ($request->isMethod('post') || $request->isMethod('put')) {
            return $this->update($request, $id);
        }
        
        return $this->edit($request, $id);
    }

    /**
     * 수정 폼 표시
     */
    public function edit(Request $request, $id)
    {
        // 기존 데이터 조회
        $old = DB::table($this->actions['table']['name'])
            ->where('id', $id)
            ->first();
        
        if (!$old) {
            return redirect($this->actions['redirect'])
                ->with('error', 'User not found');
        }
        
        $form = (array) $old;
        $form = $this->hookEditing(null, $form);
        
        return view($this->actions['view']['edit'] ?? $this->actions['view']['form'], [
            'actions' => $this->actions,
            'form' => $form,
            'id' => $id
        ]);
    }

    /**
     * 데이터 업데이트
     */
    public function update(Request $request, $id)
    {
        // 기존 데이터 조회
        $old = DB::table($this->actions['table']['name'])
            ->where('id', $id)
            ->first();
        
        if (!$old) {
            return redirect($this->actions['redirect'])
                ->with('error', 'User not found');
        }
        
        $form = $request->all();
        $form['id'] = $id;
        
        // 수정 전 훅 실행
        $form = $this->hookUpdating(null, $form, (array) $old);
        
        // id 필드 제거 (UPDATE 시 필요 없음)
        $updateData = $form;
        unset($updateData['id'], $updateData['_token'], $updateData['_method']);
        
        // 데이터베이스 업데이트
        DB::table($this->actions['table']['name'])
            ->where('id', $id)
            ->update($updateData);
        
        // 수정 후 훅 실행
        $this->hookUpdated(null, $form, (array) $old);
        
        return redirect($this->actions['redirect'])
            ->with('success', 'Admin user updated successfully');
    }

    /**
     * 수정폼이 실행될때 호출됩니다.
     */
    public function hookEditing($wire, $form)
    {
        // 현재 역할 확인
        $userId = $form['id'];

        if (DB::table('users_super')->where('user_id', $userId)->exists()) {
            $form['role'] = 'super';
        } elseif (DB::table('users_admin')->where('user_id', $userId)->exists()) {
            $form['role'] = 'admin';
        } else {
            $form['role'] = 'user';
        }

        // 비밀번호 필드는 비움
        unset($form['password']);

        return $form;
    }

    /**
     * 수정된 데이터가 DB에 적용되기 전에 호출됩니다.
     */
    public function hookUpdating($wire, $form, $old)
    {
        // 비밀번호가 입력된 경우에만 해싱
        if (!empty($form['password'])) {
            $form['password'] = Hash::make($form['password']);
        } else {
            // 비밀번호가 비어있으면 기존 값 유지
            unset($form['password']);
        }

        // timestamps 업데이트
        $form['updated_at'] = now();

        return $form;
    }

    /**
     * 수정이 완료된 후에 실행되는 후크 메소드
     */
    public function hookUpdated($wire, $form, $old)
    {
        $userId = $form['id'];
        $newRole = $form['role'] ?? 'user';

        // 기존 역할 제거
        DB::table('users_admin')->where('user_id', $userId)->delete();
        DB::table('users_super')->where('user_id', $userId)->delete();

        // 새 역할 할당
        if ($newRole === 'admin') {
            DB::table('users_admin')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } elseif ($newRole === 'super') {
            DB::table('users_super')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $form;
    }
}