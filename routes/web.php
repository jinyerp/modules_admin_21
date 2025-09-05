<?php
use Illuminate\Support\Facades\Route;

use Jiny\Admin\App\Http\Controllers\Web\Login\AdminLogin;
use Jiny\Admin\App\Http\Controllers\Web\Login\AdminAuth;
use Jiny\Admin\App\Http\Controllers\Web\Login\AdminLogout;
use Jiny\Admin\App\Http\Controllers\Web\Login\AdminPasswordChange;

// use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates;
// use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate;
// use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesShow;
// use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesEdit;
// use Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesDelete;

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
        Route::get('/login', [AdminLogin::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuth::class, 'login'])->name('admin.login.post');

        // Password related routes
        Route::prefix('login/password')->group(function () {
            // Password forgot route (누구나 접근 가능)
            Route::get('/forgot', \Jiny\Admin\App\Http\Controllers\Web\Login\AdminPasswordForgot::class)->name('admin.password.forgot');

            // Password change route (인증된 사용자만)
            Route::middleware(['auth'])->group(function () {
                Route::get('/change', [AdminPasswordChange::class, 'showChangeForm'])->name('admin.password.change');
                Route::post('/change', [AdminPasswordChange::class, 'changePassword'])->name('admin.password.change.post');
            });
        });

        // Authenticated routes (관리자 권한 필요)
        Route::middleware(['auth', 'admin'])->group(function () {
            Route::get('/dashboard', \Jiny\Admin\App\Http\Controllers\Admin\AdminDashboard\AdminDashboard::class)->name('admin.dashboard');
            Route::match(['get', 'post'], '/logout', [AdminLogout::class, 'logout'])->name('admin.logout');

            Route::get('/', function () {
                return redirect()->route('admin.dashboard');
            });
        });
    });

    // Route::prefix('admin2')->name('admin2.')->middleware(['auth', 'admin'])->group(function () {

    //     // Admin Templates CRUD Routes with Single Action Controllers
    //     Route::prefix('templates')->name('templates.')->group(function () {
    //         Route::get('/', AdminTemplates::class)->name('index');
    //         Route::match(['get', 'post'], '/create', AdminTemplatesCreate::class)->name('create');
    //         Route::get('/{id}', AdminTemplatesShow::class)->name('show');
    //         Route::match(['get', 'post', 'put'], '/{id}/edit', AdminTemplatesEdit::class)->name('edit');
    //         Route::match(['get', 'post', 'delete'], '/{id}/delete', AdminTemplatesDelete::class)->name('delete');
    //     });
    // });
});



