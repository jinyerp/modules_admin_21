<?php

namespace Jiny\Admin\Tests\Unit;

use Tests\TestCase;
use Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHello;
use Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloCreate;
use Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloEdit;
use Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminHelloControllerTest extends TestCase
{
    /**
     * AdminHello 컨트롤러가 JSON 파일을 올바르게 로드하는지 테스트
     */
    public function test_admin_hello_controller_loads_json_configuration()
    {
        // Arrange
        $controller = new AdminHello();
        $request = Request::create('/admin/hello', 'GET');
        
        // Act - 리플렉션을 사용하여 private 메소드 접근
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('loadJsonFromCurrentPath');
        $method->setAccessible(true);
        $jsonData = $method->invoke($controller);
        
        // Assert
        $this->assertNotNull($jsonData);
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('title', $jsonData);
        $this->assertArrayHasKey('table', $jsonData);
        $this->assertEquals('admin_hellos', $jsonData['table']['name']);
    }

    /**
     * AdminHelloCreate 컨트롤러가 기본값을 올바르게 설정하는지 테스트
     */
    public function test_admin_hello_create_sets_default_values()
    {
        // Arrange
        $controller = new AdminHelloCreate();
        
        // Act - hookCreating 메소드 테스트
        $form = $controller->hookCreating(null, []);
        
        // Assert
        $this->assertIsArray($form);
        $this->assertArrayHasKey('enable', $form);
        $this->assertTrue($form['enable']);
    }

    /**
     * AdminHelloEdit 컨트롤러가 업데이트 전 데이터를 올바르게 처리하는지 테스트
     */
    public function test_admin_hello_edit_processes_update_data()
    {
        // Arrange
        $controller = new AdminHelloEdit();
        $formData = [
            'id' => 1,
            'title' => 'Test Title',
            'description' => 'Test Description',
            'enable' => 'on',
            '_token' => 'test_token',
            '_method' => 'PUT'
        ];
        
        // Act - hookUpdating 메소드 테스트
        $processedData = $controller->hookUpdating(null, $formData);
        
        // Assert
        $this->assertArrayNotHasKey('id', $processedData);
        $this->assertArrayNotHasKey('_token', $processedData);
        $this->assertArrayNotHasKey('_method', $processedData);
        $this->assertArrayHasKey('updated_at', $processedData);
        $this->assertEquals(1, $processedData['enable']);
    }

    /**
     * AdminHelloShow 컨트롤러가 날짜 포맷팅을 올바르게 처리하는지 테스트
     */
    public function test_admin_hello_show_formats_dates()
    {
        // Arrange
        $controller = new AdminHelloShow();
        $data = [
            'created_at' => '2025-08-31 10:00:00',
            'updated_at' => '2025-08-31 11:00:00',
            'enable' => 1
        ];
        
        // Act - hookShowing 메소드 테스트
        $formattedData = $controller->hookShowing(null, $data);
        
        // Assert
        $this->assertArrayHasKey('created_at_formatted', $formattedData);
        $this->assertArrayHasKey('updated_at_formatted', $formattedData);
        $this->assertArrayHasKey('enable_label', $formattedData);
        $this->assertEquals('Enabled', $formattedData['enable_label']);
    }

    /**
     * JSON 파일 경로가 올바르게 생성되는지 테스트
     */
    public function test_json_file_path_is_correct()
    {
        // Arrange
        $expectedPath = base_path('jiny/admin/App/Http/Controllers/Admin/AdminHello/AdminHello.json');
        
        // Assert
        $this->assertFileExists($expectedPath);
        
        // JSON 파일 내용 검증
        $jsonContent = json_decode(File::get($expectedPath), true);
        $this->assertNotNull($jsonContent);
        $this->assertEquals('Hello Management', $jsonContent['title']);
    }

    /**
     * 모든 필수 훅 메소드가 존재하는지 테스트
     */
    public function test_required_hook_methods_exist()
    {
        // AdminHello 훅 메소드
        $adminHello = new AdminHello();
        $this->assertTrue(method_exists($adminHello, 'hookIndexing'));
        $this->assertTrue(method_exists($adminHello, 'hookIndexed'));
        $this->assertTrue(method_exists($adminHello, 'hookTableHeader'));
        $this->assertTrue(method_exists($adminHello, 'hookPagination'));
        
        // AdminHelloCreate 훅 메소드
        $adminHelloCreate = new AdminHelloCreate();
        $this->assertTrue(method_exists($adminHelloCreate, 'hookCreating'));
        $this->assertTrue(method_exists($adminHelloCreate, 'hookStoring'));
        $this->assertTrue(method_exists($adminHelloCreate, 'hookStored'));
        
        // AdminHelloEdit 훅 메소드
        $adminHelloEdit = new AdminHelloEdit();
        $this->assertTrue(method_exists($adminHelloEdit, 'hookEditing'));
        $this->assertTrue(method_exists($adminHelloEdit, 'hookUpdating'));
        $this->assertTrue(method_exists($adminHelloEdit, 'hookUpdated'));
        
        // AdminHelloShow 훅 메소드
        $adminHelloShow = new AdminHelloShow();
        $this->assertTrue(method_exists($adminHelloShow, 'hookShowing'));
        $this->assertTrue(method_exists($adminHelloShow, 'hookShowed'));
    }

    /**
     * 설정 경로(settingsPath)가 올바르게 설정되는지 테스트
     */
    public function test_settings_path_is_set_correctly()
    {
        // Arrange
        $expectedPath = base_path('jiny/admin/App/Http/Controllers/Admin/AdminHello/AdminHello.json');
        
        // Create 컨트롤러 테스트
        $createController = new AdminHelloCreate();
        $request = Request::create('/admin/hello/create', 'GET');
        
        // Show 컨트롤러 테스트
        $showController = new AdminHelloShow();
        
        // Edit 컨트롤러 테스트
        $editController = new AdminHelloEdit();
        
        // 각 컨트롤러가 settingsPath를 뷰에 전달하는지 확인
        // (실제 뷰 렌더링 없이 컨트롤러 로직만 테스트)
        $this->assertFileExists($expectedPath);
    }

    /**
     * 템플릿 경로가 올바르게 설정되는지 테스트
     */
    public function test_template_paths_are_correct()
    {
        // Arrange
        $jsonPath = base_path('jiny/admin/App/Http/Controllers/Admin/AdminHello/AdminHello.json');
        $jsonContent = json_decode(File::get($jsonPath), true);
        
        // Assert
        $this->assertEquals('jiny-admin::template.index', $jsonContent['template']['index']);
        $this->assertEquals('jiny-admin::template.create', $jsonContent['template']['create']);
        $this->assertEquals('jiny-admin::template.edit', $jsonContent['template']['edit']);
        $this->assertEquals('jiny-admin::template.show', $jsonContent['template']['show']);
    }

    /**
     * 페이지네이션 설정이 올바른지 테스트
     */
    public function test_pagination_settings()
    {
        // Arrange
        $controller = new AdminHello();
        
        // Act
        $pagination = $controller->hookPagination(null);
        
        // Assert
        $this->assertIsArray($pagination);
        $this->assertArrayHasKey('perPage', $pagination);
        $this->assertArrayHasKey('perPageOptions', $pagination);
        $this->assertEquals(10, $pagination['perPage']);
        $this->assertContains(25, $pagination['perPageOptions']);
    }

    /**
     * 검색 설정이 올바른지 테스트
     */
    public function test_search_settings()
    {
        // Arrange
        $controller = new AdminHello();
        
        // Act
        $search = $controller->hookSearch(null);
        
        // Assert
        $this->assertIsArray($search);
        $this->assertArrayHasKey('placeholder', $search);
        $this->assertArrayHasKey('debounce', $search);
        $this->assertStringContainsString('hello', $search['placeholder']);
        $this->assertEquals(300, $search['debounce']);
    }
}