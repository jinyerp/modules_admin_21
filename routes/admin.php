<?php
use Illuminate\Support\Facades\Route;

// Admin Test Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'test'], function () {
        Route::get('/', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTest\AdminTest::class)
            ->name('admin.test');
        
        Route::get('/create', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTest\AdminTestCreate::class)
            ->name('admin.test.create');
        
        Route::get('/{id}/edit', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTest\AdminTestEdit::class)
            ->name('admin.test.edit');
        
        Route::get('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTest\AdminTestShow::class)
            ->name('admin.test.show');
        
        Route::delete('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTest\AdminTestDelete::class)
            ->name('admin.test.delete');
    });
});

// Admin2 Templates Routes
Route::middleware(['web'])->prefix('admin2')->group(function () {
    Route::group(['prefix' => 'templates'], function () {
        Route::get('/', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates::class)
            ->name('admin2.templates');
        
        Route::get('/create', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate::class)
            ->name('admin2.templates.create');
        
        Route::get('/{id}/edit', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesEdit::class)
            ->name('admin2.templates.edit');
        
        Route::get('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesShow::class)
            ->name('admin2.templates.show');
        
        Route::delete('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesDelete::class)
            ->name('admin2.templates.delete');
    });
});

// Admin Hello Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'hello'], function () {
        Route::get('/', \Jiny\Admin2\App\Http\Controllers\Admin\AdminHello\AdminHello::class)
            ->name('admin.hello');
        
        Route::get('/create', \Jiny\Admin2\App\Http\Controllers\Admin\AdminHello\AdminHelloCreate::class)
            ->name('admin.hello.create');
        
        Route::get('/{id}/edit', \Jiny\Admin2\App\Http\Controllers\Admin\AdminHello\AdminHelloEdit::class)
            ->name('admin.hello.edit');
        
        Route::get('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminHello\AdminHelloShow::class)
            ->name('admin.hello.show');
        
        Route::delete('/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminHello\AdminHelloDelete::class)
            ->name('admin.hello.delete');
    });
});
