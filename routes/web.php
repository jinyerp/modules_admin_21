<?php
use Illuminate\Support\Facades\Route;

use Jiny\Admin\App\Http\Controllers\Web\Login\AdminLoginController;
use Jiny\Admin\App\Http\Controllers\Web\Login\AdminAuthController;

use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates;
use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate;
use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesShow;
use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesEdit;
use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesDelete;

/*
|--------------------------------------------------------------------------
| Admin Domain Web Routes
|--------------------------------------------------------------------------
*/

// Web 미들웨어 그룹 적용
Route::middleware(['web'])->group(function () {
    
    // Admin Login Routes
    Route::prefix('admin')->group(function () {
        // Login routes (누구나 접근 가능, 컨트롤러에서 처리)
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

        // Authenticated routes (관리자 권한 필요)
        Route::middleware(['auth', 'admin'])->group(function () {
            Route::get('/dashboard', \Jiny\Admin\App\Http\Controllers\Admin\AdminDashboard\AdminDashboard::class)->name('admin.dashboard');
            Route::match(['get', 'post'], '/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
            
            Route::get('/', function () {
                return redirect()->route('admin.dashboard');
            });
        });
    });

    Route::prefix('admin2')->name('admin2.')->middleware(['auth', 'admin'])->group(function () {

        // Admin Templates CRUD Routes with Single Action Controllers
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', AdminTemplates::class)->name('index');
            Route::match(['get', 'post'], '/create', AdminTemplatesCreate::class)->name('create');
            Route::get('/{id}', AdminTemplatesShow::class)->name('show');
            Route::match(['get', 'post', 'put'], '/{id}/edit', AdminTemplatesEdit::class)->name('edit');
            Route::match(['get', 'post', 'delete'], '/{id}/delete', AdminTemplatesDelete::class)->name('delete');
        });
    });
});



