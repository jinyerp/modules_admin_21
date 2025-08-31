<?php

namespace Jiny\Admin\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminHelloTest extends TestCase
{
    use RefreshDatabase;

    /**
     * admin:make 명령어로 생성된 Hello 기능이 정상 작동하는지 테스트
     */
    public function test_admin_hello_index_page_loads_successfully()
    {
        // Act: /admin/hello 페이지 접근
        $response = $this->get('/admin/hello');

        // Assert: 페이지가 정상적으로 로드되는지 확인
        $response->assertStatus(200);
        $response->assertViewIs('jiny-admin::template.index');
        $response->assertViewHas('jsonData');
        $response->assertViewHas('jsonPath');
        $response->assertViewHas('settingsPath');
    }

    /**
     * Hello 생성 페이지가 정상적으로 로드되는지 테스트
     */
    public function test_admin_hello_create_page_loads_successfully()
    {
        // Act: /admin/hello/create 페이지 접근
        $response = $this->get('/admin/hello/create');

        // Assert: 생성 페이지가 정상적으로 로드되는지 확인
        $response->assertStatus(200);
        $response->assertViewIs('jiny-admin::template.create');
        $response->assertViewHas('form');
        $response->assertViewHas('settingsPath');
        $response->assertSee('Create New Hello');
    }

    /**
     * Hello 데이터 생성 후 상세보기 페이지 테스트
     */
    public function test_admin_hello_show_page_loads_with_data()
    {
        // Arrange: 테스트 데이터 생성
        $helloId = DB::table('admin_hellos')->insertGetId([
            'title' => 'Test Hello',
            'description' => 'Test Description',
            'enable' => 1,
            'pos' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Act: 상세보기 페이지 접근
        $response = $this->get("/admin/hello/{$helloId}");

        // Assert: 상세 페이지가 정상적으로 로드되는지 확인
        $response->assertStatus(200);
        $response->assertViewIs('jiny-admin::template.show');
        $response->assertViewHas('data');
        $response->assertViewHas('settingsPath');
        $response->assertSee('Test Hello');
    }

    /**
     * Hello 수정 페이지가 정상적으로 로드되는지 테스트
     */
    public function test_admin_hello_edit_page_loads_with_data()
    {
        // Arrange: 테스트 데이터 생성
        $helloId = DB::table('admin_hellos')->insertGetId([
            'title' => 'Test Hello Edit',
            'description' => 'Test Description for Edit',
            'enable' => 1,
            'pos' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Act: 수정 페이지 접근
        $response = $this->get("/admin/hello/{$helloId}/edit");

        // Assert: 수정 페이지가 정상적으로 로드되는지 확인
        $response->assertStatus(200);
        $response->assertViewIs('jiny-admin::template.edit');
        $response->assertViewHas('form');
        $response->assertViewHas('settingsPath');
        $response->assertSee('Edit Hello');
    }

    /**
     * 존재하지 않는 Hello 데이터에 접근 시 리다이렉트 테스트
     */
    public function test_admin_hello_show_redirects_when_not_found()
    {
        // Act: 존재하지 않는 ID로 접근
        $response = $this->get('/admin/hello/99999');

        // Assert: 목록 페이지로 리다이렉트되는지 확인
        $response->assertStatus(302);
        $response->assertRedirect('/admin/hello');
        $response->assertSessionHas('error');
    }

    /**
     * JSON 설정 파일이 올바르게 로드되는지 테스트
     */
    public function test_admin_hello_json_configuration_exists()
    {
        // Arrange: JSON 파일 경로
        $jsonPath = base_path('jiny/admin/App/Http/Controllers/Admin/AdminHello/AdminHello.json');

        // Assert: JSON 파일이 존재하는지 확인
        $this->assertFileExists($jsonPath);

        // JSON 파일 내용 검증
        $jsonContent = json_decode(File::get($jsonPath), true);
        $this->assertNotNull($jsonContent);
        $this->assertArrayHasKey('title', $jsonContent);
        $this->assertArrayHasKey('table', $jsonContent);
        $this->assertArrayHasKey('template', $jsonContent);
        $this->assertEquals('admin_hellos', $jsonContent['table']['name']);
    }

    /**
     * 생성된 컨트롤러 파일들이 모두 존재하는지 테스트
     */
    public function test_admin_hello_controller_files_exist()
    {
        $controllerPath = base_path('jiny/admin/App/Http/Controllers/Admin/AdminHello');
        
        $requiredFiles = [
            'AdminHello.php',
            'AdminHelloCreate.php',
            'AdminHelloEdit.php',
            'AdminHelloShow.php',
            'AdminHelloDelete.php',
            'AdminHello.json'
        ];

        foreach ($requiredFiles as $file) {
            $this->assertFileExists($controllerPath . '/' . $file);
        }
    }

    /**
     * 생성된 뷰 파일들이 모두 존재하는지 테스트
     */
    public function test_admin_hello_view_files_exist()
    {
        $viewPath = base_path('jiny/admin/resources/views/admin/hello');
        
        $requiredViews = [
            'table.blade.php',
            'create.blade.php',
            'edit.blade.php',
            'show.blade.php',
            'search.blade.php'
        ];

        foreach ($requiredViews as $view) {
            $this->assertFileExists($viewPath . '/' . $view);
        }
    }

    /**
     * 라우트가 올바르게 등록되었는지 테스트
     */
    public function test_admin_hello_routes_are_registered()
    {
        // 라우트 이름들 확인
        $routes = [
            'admin.hello' => 'GET',
            'admin.hello.create' => 'GET',
            'admin.hello.edit' => 'GET',
            'admin.hello.show' => 'GET',
            'admin.hello.delete' => 'DELETE'
        ];

        foreach ($routes as $routeName => $method) {
            $this->assertTrue(
                \Route::has($routeName),
                "Route {$routeName} is not registered"
            );
        }
    }

    /**
     * Settings Drawer 컴포넌트가 포함되어 있는지 테스트
     */
    public function test_admin_hello_includes_settings_drawer()
    {
        // Act: 생성 페이지 접근
        $response = $this->get('/admin/hello/create');

        // Assert: settingsPath 변수가 전달되는지 확인
        $response->assertStatus(200);
        $response->assertViewHas('settingsPath');
        
        $viewData = $response->viewData('jsonData');
        $this->assertArrayHasKey('create', $viewData);
        $this->assertTrue($viewData['create']['enableSettingsDrawer'] ?? false);
    }

    /**
     * 빈 페이지 오류가 발생하지 않는지 테스트
     */
    public function test_admin_hello_no_blank_page_errors()
    {
        // 모든 페이지에서 필수 변수들이 전달되는지 확인
        $pages = [
            '/admin/hello' => ['jsonData', 'jsonPath', 'settingsPath'],
            '/admin/hello/create' => ['jsonData', 'jsonPath', 'settingsPath', 'form'],
        ];

        foreach ($pages as $url => $requiredVars) {
            $response = $this->get($url);
            $response->assertStatus(200);
            
            foreach ($requiredVars as $var) {
                $response->assertViewHas($var);
            }
        }
    }

    /**
     * 데이터베이스 테이블이 올바르게 생성되었는지 테스트
     */
    public function test_admin_hellos_table_structure()
    {
        // 테이블 존재 확인
        $this->assertTrue(
            \Schema::hasTable('admin_hellos'),
            'admin_hellos table does not exist'
        );

        // 필수 컬럼 확인
        $requiredColumns = [
            'id', 'enable', 'title', 'description', 
            'pos', 'created_at', 'updated_at'
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                \Schema::hasColumn('admin_hellos', $column),
                "Column {$column} does not exist in admin_hellos table"
            );
        }
    }
}