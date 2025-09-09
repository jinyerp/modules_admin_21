<?php

use Illuminate\Support\Facades\Route;

// // Admin Test Routes
// Route::middleware(['web', 'admin'])->prefix('admin')->group(function () {
//     Route::group(['prefix' => 'test'], function () {
//         Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTest::class)
//             ->name('admin.test');

//         Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestCreate::class)
//             ->name('admin.test.create');

//         Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestEdit::class)
//             ->name('admin.test.edit');

//         Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestShow::class)
//             ->name('admin.test.show');

//         Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestDelete::class)
//             ->name('admin.test.delete');
//     });
// });

// // Admin Templates Routes
// Route::middleware(['web', 'admin'])->prefix('admin')->group(function () {
//     Route::group(['prefix' => 'templates'], function () {
//         Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplates::class)
//             ->name('admin.templates');

//         Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate::class)
//             ->name('admin.templates.create');

//         Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesEdit::class)
//             ->name('admin.templates.edit');

//         Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesShow::class)
//             ->name('admin.templates.show');

//         Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesDelete::class)
//             ->name('admin.templates.delete');
//     });
// });

// // Admin Hello Routes
// Route::middleware(['web', 'admin'])->prefix('admin')->group(function () {
//     Route::group(['prefix' => 'hello'], function () {
//         Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHello::class)
//             ->name('admin.hello');

//         Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloCreate::class)
//             ->name('admin.hello.create');

//         Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloEdit::class)
//             ->name('admin.hello.edit');

//         Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloShow::class)
//             ->name('admin.hello.show');

//         Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloDelete::class)
//             ->name('admin.hello.delete');
//     });
// });

// Admin User Type Routes
Route::middleware(['web', 'admin'])->prefix('admin/user')->group(function () {
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
Route::middleware(['web', 'admin'])->prefix('admin')->group(function () {
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

// Admin User Logs Routes (auth required)
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => 'logs'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminUserLogs\AdminUserLogs::class)
            ->name('admin.user.logs');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUserLogs\AdminUserLogsShow::class)
            ->name('admin.user.logs.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUserLogs\AdminUserLogsDelete::class)
            ->name('admin.user.logs.delete');
    });
});

// Admin IP Whitelist Routes (보안 설정)
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/security')->group(function () {
    Route::group(['prefix' => 'ip-whitelist'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpWhitelist\AdminIpWhitelist::class)
            ->name('admin.security.ip-whitelist');

        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpWhitelist\AdminIpWhitelistCreate::class)
            ->name('admin.security.ip-whitelist.create');

        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpWhitelist\AdminIpWhitelistEdit::class)
            ->name('admin.security.ip-whitelist.edit');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpWhitelist\AdminIpWhitelistShow::class)
            ->name('admin.security.ip-whitelist.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpWhitelist\AdminIpWhitelistDelete::class)
            ->name('admin.security.ip-whitelist.delete');
    });
});

// Admin User 2FA Routes (관리자 전용)
Route::middleware(['web'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => '2fa'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2fa::class)
            ->name('admin.user.2fa');

        Route::get('/create/{id?}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faCreate::class)
            ->name('admin.user.2fa.create');

        Route::get('/{id}/edit', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'edit'])
            ->name('admin.user.2fa.edit');

        Route::post('/{id}/generate', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'generate'])
            ->name('admin.user.2fa.generate');

        Route::post('/{id}/store', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'store'])
            ->name('admin.user.2fa.store');

        Route::post('/{id}/regenerate-backup', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'regenerateBackup'])
            ->name('admin.user.2fa.regenerate-backup');

        Route::delete('/{id}/disable', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'disable'])
            ->name('admin.user.2fa.disable');

        Route::delete('/{id}/force-disable', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'forceDisable'])
            ->name('admin.user.2fa.force-disable');

        Route::get('/{id}/status', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'status'])
            ->name('admin.user.2fa.status');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faShow::class)
            ->name('admin.user.2fa.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faDelete::class)
            ->name('admin.user.2fa.delete');
    });
});

// 2FA 인증 라우트 (로그인 과정)
Route::middleware(['web'])->prefix('admin/login')->group(function () {
    Route::get('/2fa/challenge', [\Jiny\Admin\App\Http\Controllers\Web\Login\Admin2FA::class, 'showChallenge'])
        ->name('admin.2fa.challenge');

    Route::post('/2fa/verify', [\Jiny\Admin\App\Http\Controllers\Web\Login\Admin2FA::class, 'verify'])
        ->name('admin.2fa.verify');
});

// Admin User Sessions Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => 'sessions'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminSessions\AdminSessions::class)
            ->name('admin.user.sessions');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminSessions\AdminSessionsShow::class)
            ->name('admin.user.sessions.show');

        Route::post('/{id}/terminate', \Jiny\Admin\App\Http\Controllers\Admin\AdminSessions\AdminSessionsDelete::class)
            ->name('admin.user.sessions.terminate');
    });
});

// Admin User Stats Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user')->group(function () {
    Route::get('/stats', \Jiny\Admin\App\Http\Controllers\Admin\AdminStats\AdminStats::class)
        ->name('admin.user.stats');
});

// Admin Password Logs Routes (관리자 전용)
Route::middleware(['web', 'auth'])->prefix('admin/user/password')->group(function () {
    Route::group(['prefix' => 'logs'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminPasswordLogs\AdminPasswordLogs::class)
            ->name('admin.user.password.logs');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminPasswordLogs\AdminPasswordLogsShow::class)
            ->name('admin.user.password.logs.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminPasswordLogs\AdminPasswordLogsDelete::class)
            ->name('admin.user.password.logs.delete');

        Route::post('/{id}/unblock', \Jiny\Admin\App\Http\Controllers\Admin\AdminPasswordLogs\AdminPasswordLogsUnblock::class)
            ->name('admin.user.password.logs.unblock');

        Route::post('/bulk/unblock', [\Jiny\Admin\App\Http\Controllers\Admin\AdminPasswordLogs\AdminPasswordLogsUnblock::class, 'bulk'])
            ->name('admin.user.password.logs.bulk-unblock');
    });
});

// Admin User Password Management Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => 'password'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminUserPassword\AdminUserPassword::class)
            ->name('admin.user.password');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminUserPassword\AdminUserPasswordShow::class)
            ->name('admin.user.password.show');
    });
});

// Admin Avatar Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user')->group(function () {
    Route::group(['prefix' => 'avatar'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminAvatar\AdminAvatar::class)
            ->name('admin.avatar');

        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminAvatar\AdminAvatarCreate::class)
            ->name('admin.avatar.create');

        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminAvatar\AdminAvatarEdit::class)
            ->name('admin.avatar.edit');

        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminAvatar\AdminAvatarShow::class)
            ->name('admin.avatar.show');

        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminAvatar\AdminAvatarDelete::class)
            ->name('admin.avatar.delete');
    });
});

// Admin Settings Mail Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/settings')->group(function () {
    Route::get('/mail', \Jiny\Admin\App\Http\Controllers\Admin\AdminSettingsMail\AdminSettingsMail::class)
        ->name('admin.settings.mail');
    
    Route::post('/mail/update', [\Jiny\Admin\App\Http\Controllers\Admin\AdminSettingsMail\AdminSettingsMail::class, 'update'])
        ->name('admin.settings.mail.update');
    
    Route::post('/mail/test', [\Jiny\Admin\App\Http\Controllers\Admin\AdminSettingsMail\AdminSettingsMail::class, 'test'])
        ->name('admin.settings.mail.test');
});
