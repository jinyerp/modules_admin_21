<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jiny\Admin\Http\Controllers\AdminController;

/**
 * AdminUsers Create Controller
 * 
 * 관리자 회원 생성 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminUsersCreate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setVisit($this);

        // 테이블 설정
        $this->actions['table']['name'] = "users";
        
        // 뷰 파일 설정
        $this->actions['view']['form'] = "jiny-admin2::admin.admin_users.form";
        $this->actions['view']['create'] = "jiny-admin2::admin.admin_users.create";

        // 페이지 제목 설정
        $this->actions['title'] = "Create Admin User";
        $this->actions['subtitle'] = "Add new administrator account";

        // 생성 후 리다이렉트 경로
        $this->actions['redirect'] = "/admin/users";
    }

    /**
     * Single Action __invoke method
     * 생성 폼 및 저장 처리
     */
    public function __invoke(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->store($request);
        }
        
        return $this->create($request);
    }

    /**
     * 생성 폼 표시
     */
    public function create(Request $request)
    {
        $form = $this->hookCreating(null, []);
        
        return view($this->actions['view']['create'] ?? $this->actions['view']['form'], [
            'actions' => $this->actions,
            'form' => $form
        ]);
    }

    /**
     * 데이터 저장
     */
    public function store(Request $request)
    {
        $form = $request->all();
        
        // 저장 전 훅 실행
        $form = $this->hookStoring(null, $form);
        
        // 데이터베이스에 저장
        $user = DB::table($this->actions['table']['name'])->insertGetId($form);
        $form['id'] = $user;
        
        // 저장 후 훅 실행
        $this->hookStored(null, $form);
        
        return redirect($this->actions['redirect'])
            ->with('success', 'Admin user created successfully');
    }

    /**
     * 생성폼이 실행될때 호출됩니다.
     */
    public function hookCreating($wire, $value)
    {
        // 기본값 설정
        $form = [
            'role' => 'user',
            'email_verified_at' => now()
        ];

        return $form;
    }

    /**
     * 신규 데이터 DB 삽입전에 호출됩니다.
     */
    public function hookStoring($wire, $form)
    {
        // 비밀번호 해싱
        if (isset($form['password'])) {
            $form['password'] = Hash::make($form['password']);
        }

        // 이메일 인증 시간 설정
        if (!isset($form['email_verified_at'])) {
            $form['email_verified_at'] = now();
        }

        // timestamps 추가
        $form['created_at'] = now();
        $form['updated_at'] = now();

        return $form;
    }

    /**
     * 신규 데이터 DB 삽입후에 호출됩니다.
     */
    public function hookStored($wire, $form)
    {
        $userId = $form['id'];
        $role = $form['role'] ?? 'user';

        // 역할 테이블에 추가
        if ($role === 'admin') {
            DB::table('users_admin')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } elseif ($role === 'super') {
            DB::table('users_super')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $form;
    }
}