<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminUsers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Jiny\Admin\App\Services\PasswordValidator;

/**
 * AdminUsers Edit Controller
 *
 * User 수정 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin
 */
class AdminUsersEdit extends Controller
{
    private $jsonData;

    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 AdminUsers.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUsers.json';

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
     * 수정 폼 표시
     */
    public function __invoke(Request $request, $id)
    {
        // 데이터베이스에서 데이터 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_usertypes';
        $data = DB::table($tableName)
            ->where('id', $id)
            ->first();

        if (!$data) {
            if (isset($this->jsonData['route']['name'])) {
                $redirectUrl = route($this->jsonData['route']['name']);
            } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
                $redirectUrl = route($this->jsonData['route']);
            } else {
                $redirectUrl = '/admin/users';
            }
            return redirect($redirectUrl)
                ->with('error', 'User을(를) 찾을 수 없습니다.');
        }

        // 객체를 배열로 변환
        $form = (array) $data;

        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            // 이전 버전 호환성
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }

        // template.edit view 경로 확인
        if(!isset($this->jsonData['template']['edit'])) {
            return response("Error: 화면을 출력하기 위한 template.edit 설정이 필요합니다.", 500);
        }

        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUsers.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로

        return view($this->jsonData['template']['edit'], [
            'controllerClass' => static::class,  // 현재 컨트롤러 클래스 전달
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'form' => $form,
            'id' => $id,
            'title' => 'Edit User',
            'subtitle' => 'User을(를) 수정합니다.'
        ]);
    }

    /**
     * 수정폼이 실행될때 호출됩니다.
     */
    public function hookEditing($wire, $form)
    {
        // 사용자 타입 목록 가져오기
        $userTypes = DB::table('admin_user_types')
            ->where('enable', 1)
            ->orderBy('level', 'desc')
            ->get();

        // View에 전달할 데이터 설정
        if ($wire) {
            $wire->userTypes = $userTypes;
        }

        // isAdmin 필드를 boolean으로 변환
        $form['isAdmin'] = (bool) ($form['isAdmin'] ?? false);

        // 패스워드 필드는 비워둠 (보안상 기존 패스워드를 표시하지 않음)
        unset($form['password']);

        return $form;
    }

    /**
     * 데이터 업데이트 전에 호출됩니다.
     *
     * @return array|string 성공시 수정된 form 배열, 실패시 에러 메시지 문자열
     */
    public function hookUpdating($wire, $form)
    {
        // 기존 데이터 가져오기 (user type 변경 감지를 위해)
        $tableName = $this->jsonData['table']['name'] ?? 'users';
        $oldData = DB::table($tableName)
            ->where('id', $wire->id ?? $form['id'] ?? 0)
            ->first();

        // 이전 utype 값을 wire에 저장 (hookUpdated에서 사용)
        if ($wire && $oldData) {
            $wire->oldUtype = $oldData->utype;
        }

        // 디버깅: 패스워드 필드 확인
        \Log::info('hookUpdating - form keys: ' . implode(', ', array_keys($form)));
        if (isset($form['password'])) {
            \Log::info('hookUpdating - password value: ' . $form['password']);
        }

        // 패스워드가 입력된 경우에만 검증 및 해싱
        if (isset($form['password']) && !empty($form['password'])) {
            \Log::info('Password validation starting for: ' . $form['password']);
            $passwordValidator = new PasswordValidator();

            // 사용자 정보 준비 (유사성 체크용)
            $userData = [
                'name' => $form['name'] ?? '',
                'email' => $form['email'] ?? ''
            ];

            // 패스워드 유효성 검증
            if (!$passwordValidator->validate($form['password'], $userData)) {
                // 검증 실패 시 에러 메시지 문자열 반환
                $errors = $passwordValidator->getErrors();
                $errorMessage = '패스워드 검증 실패: ' . implode(' ', $errors);

                \Log::error('Password validation failed: ' . $errorMessage);

                // Livewire 컴포넌트에 에러 전달
                if ($wire && method_exists($wire, 'addError')) {
                    foreach ($errors as $error) {
                        $wire->addError('form.password', $error);
                    }
                }

                // 에러 메시지 문자열 반환 (배열이 아님)
                //dd($errorMessage);
                return $errorMessage;
            }

            // 검증 통과 시 패스워드 해싱
            $form['password'] = Hash::make($form['password']);
        } else {
            // 패스워드가 비어있으면 업데이트하지 않음
            unset($form['password']);
        }

        // isAdmin 필드 처리 (체크박스)
        $form['isAdmin'] = isset($form['isAdmin']) ? 1 : 0;

        // ID 제거 (업데이트 시 필요 없음)
        unset($form['id']);
        unset($form['_token']);
        unset($form['_method']);
        unset($form['password_confirmation']);

        // updated_at 타임스탬프 업데이트
        $form['updated_at'] = now();

        // 성공: 배열 반환
        return $form;
    }

    /**
     * 데이터 업데이트 후에 호출됩니다.
     */
    public function hookUpdated($wire, $form)
    {
        // 사용자 타입이 변경된 경우 카운트 업데이트
        $oldUtype = $wire->oldUtype ?? null;
        $newUtype = $form['utype'] ?? null;

        if ($oldUtype !== $newUtype) {
            // 이전 타입의 카운트 감소
            if ($oldUtype) {
                DB::table('admin_user_types')
                    ->where('code', $oldUtype)
                    ->where('user_count', '>', 0)
                    ->decrement('user_count');
            }

            // 새 타입의 카운트 증가
            if ($newUtype) {
                DB::table('admin_user_types')
                    ->where('code', $newUtype)
                    ->increment('user_count');
            }
        }

        return $form;
    }
}
