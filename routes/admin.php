<?php
use Illuminate\Support\Facades\Route;

// Admin Test Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'test'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTest::class)
            ->name('admin.test');

        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestCreate::class)
            ->name('admin.test.create');

        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestEdit::class)
            ->name('admin.test.edit');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestShow::class)
            ->name('admin.test.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestDelete::class)
            ->name('admin.test.delete');
    });
});

// Admin Templates Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'templates'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates::class)
            ->name('admin.templates');

        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate::class)
            ->name('admin.templates.create');

        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesEdit::class)
            ->name('admin.templates.edit');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesShow::class)
            ->name('admin.templates.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesDelete::class)
            ->name('admin.templates.delete');
    });
});

// Admin Hello Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'hello'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHello::class)
            ->name('admin.hello');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloCreate::class)
            ->name('admin.hello.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloEdit::class)
            ->name('admin.hello.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloShow::class)
            ->name('admin.hello.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloDelete::class)
            ->name('admin.hello.delete');
    });
});

// Admin User Type Routes
Route::middleware(['web'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => 'type'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertype::class)
            ->name('admin.user.type');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertypeCreate::class)
            ->name('admin.user.type.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertypeEdit::class)
            ->name('admin.user.type.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertypeShow::class)
            ->name('admin.user.type.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertypeDelete::class)
            ->name('admin.user.type.delete');
    });
});

// Admin Users Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsers::class)
            ->name('admin.users');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsersCreate::class)
            ->name('admin.users.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsersEdit::class)
            ->name('admin.users.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsersShow::class)
            ->name('admin.users.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsersDelete::class)
            ->name('admin.users.delete');
    });
});
