<?php
use Illuminate\Support\Facades\Route;

// dd("admin");

$admin = Prefix("admin");
Route::middleware(['web'])->name('admin')
->prefix($admin)->group(function () {
//     // Admin Users Management - Single Action Controllers

    // 목록 조회
    Route::get('/users', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsers::class)
        ->name('.users');

//     // 생성
//     Route::get('/users/create', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersCreate::class)
//         ->name('.users.create');
//     Route::post('/users/create', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersCreate::class)
//         ->name('.users.store');

//     // 수정
//     Route::get('/users/{id}/edit', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersEdit::class)
//         ->name('.users.edit');
//     Route::put('/users/{id}/edit', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersEdit::class)
//         ->name('.users.update');

//     // 삭제
//     Route::delete('/users/{id}', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersDelete::class)
//         ->name('.users.delete');
//     Route::delete('/users', \Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers\AdminUsersDelete::class)
//         ->name('.users.delete.multiple');
});
