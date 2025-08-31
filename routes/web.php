<?php
use Illuminate\Support\Facades\Route;


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

    Route::prefix('admin2')->name('admin2.')->group(function () {

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



