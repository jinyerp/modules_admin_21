<?php

use Illuminate\Support\Facades\Route;
use Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\Admin2FAController;

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

        Route::post('/{id}/show-qr', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'showQr'])
            ->name('admin.user.2fa.show-qr');

        Route::post('/{id}/regenerate-qr', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'regenerateQr'])
            ->name('admin.user.2fa.regenerate-qr');

        Route::post('/{id}/confirm-regenerate', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\AdminUser2faEdit::class, 'confirmRegenerateQr'])
            ->name('admin.user.2fa.confirm-regenerate');

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

// 2FA 추가 라우트 (admin-2fa.php에서 통합)
Route::prefix('admin/users/{userId}/2fa')->middleware(['web', 'auth'])->group(function () {
    // SMS 관련
    Route::post('/send-sms', [Admin2FAController::class, 'sendSmsCode'])
        ->name('admin.user.2fa.send-sms');
    
    Route::post('/verify-sms', [Admin2FAController::class, 'verifySmsCode'])
        ->name('admin.user.2fa.verify-sms');
    
    // Email 관련
    Route::post('/send-email', [Admin2FAController::class, 'sendEmailCode'])
        ->name('admin.user.2fa.send-email');
    
    Route::post('/verify-email', [Admin2FAController::class, 'verifyEmailCode'])
        ->name('admin.user.2fa.verify-email');
    
    // 2FA 방법 변경
    Route::post('/change-method', [Admin2FAController::class, 'changeMethod'])
        ->name('admin.user.2fa.change-method');
    
    // 백업 코드 관련
    Route::post('/regenerate-backup', [Admin2FAController::class, 'regenerateBackupCodes'])
        ->name('admin.user.2fa.regenerate-backup');
    
    Route::get('/download-backup', [Admin2FAController::class, 'downloadBackupCodes'])
        ->name('admin.user.2fa.download-backup');
    
    // 2FA 상태 확인 (기존 라우트와 중복 - 제거)
    // Route::get('/status', [Admin2FAController::class, 'getStatus'])
    //     ->name('admin.user.2fa.status');
    
    // 전화번호 업데이트
    Route::post('/update-phone', [Admin2FAController::class, 'updatePhoneNumber'])
        ->name('admin.user.profile.update-phone');
});

// 만료된 코드 정리 (크론잡용 - API 미들웨어 사용)
Route::get('/admin/2fa/cleanup', [\Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\Admin2FAController::class, 'cleanupExpiredCodes'])
    ->name('admin.2fa.cleanup')
    ->middleware(['api']);

// 2FA 인증 라우트 (로그인 과정)
Route::middleware(['web'])->prefix('admin/login')->group(function () {
    Route::get('/2fa/challenge', [\jiny\admin\App\Http\Controllers\Web\Login\Admin2FA::class, 'showChallenge'])
        ->name('admin.2fa.challenge');

    Route::post('/2fa/verify', [\jiny\admin\App\Http\Controllers\Web\Login\Admin2FA::class, 'verify'])
        ->name('admin.2fa.verify');
});

// 계정 잠금 해제 라우트
Route::middleware(['web'])->prefix('account/unlock')->group(function () {
    // 잠금 해제 페이지 표시
    Route::get('/{token}', [\jiny\admin\App\Http\Controllers\Web\Login\UnlockAccount::class, 'show'])
        ->name('account.unlock.show');
    
    // 잠금 해제 처리
    Route::post('/', [\jiny\admin\App\Http\Controllers\Web\Login\UnlockAccount::class, 'unlock'])
        ->name('account.unlock.process');
    
    // 새 잠금 해제 링크 요청 페이지
    Route::get('/request/new', [\jiny\admin\App\Http\Controllers\Web\Login\UnlockAccount::class, 'requestForm'])
        ->name('account.unlock.request');
    
    // 새 잠금 해제 링크 발송
    Route::post('/request/send', [\jiny\admin\App\Http\Controllers\Web\Login\UnlockAccount::class, 'sendUnlockLink'])
        ->name('account.unlock.send');
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

// Admin CAPTCHA Logs Routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/user/captcha')->group(function () {
    Route::group(['prefix' => 'logs'], function () {
        Route::get('/', [\Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogs::class, 'index'])
            ->name('admin.captcha.logs');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogsCreate::class)
            ->name('admin.captcha.logs.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogsEdit::class)
            ->name('admin.captcha.logs.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogsShow::class)
            ->name('admin.captcha.logs.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogsDelete::class)
            ->name('admin.captcha.logs.delete');
        
        Route::get('/list', [\Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogs::class, 'list'])
            ->name('admin.captcha.logs.list');
        
        Route::get('/export', [\Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogs::class, 'export'])
            ->name('admin.captcha.logs.export');
        
        Route::post('/block-ip', [\Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogs::class, 'blockIp'])
            ->name('admin.captcha.logs.block');
        
        Route::post('/cleanup', [\Jiny\Admin\App\Http\Controllers\Admin\AdminCaptchaLogs\AdminCaptchaLogs::class, 'cleanupLogs'])
            ->name('admin.captcha.logs.cleanup');
    });
});

// Admin SMS Routes
Route::middleware(['web'])->prefix('admin/sms')->group(function () {
    // SMS Provider Routes
    Route::group(['prefix' => 'provider'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsProvider\AdminSmsProvider::class)
            ->name('admin.sms.provider');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsProvider\AdminSmsProviderCreate::class)
            ->name('admin.sms.provider.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsProvider\AdminSmsProviderEdit::class)
            ->name('admin.sms.provider.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsProvider\AdminSmsProviderShow::class)
            ->name('admin.sms.provider.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsProvider\AdminSmsProviderDelete::class)
            ->name('admin.sms.provider.delete');
    });
    
    // SMS Send Routes  
    Route::group(['prefix' => 'send'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSend::class)
            ->name('admin.sms.send');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSendCreate::class)
            ->name('admin.sms.send.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSendEdit::class)
            ->name('admin.sms.send.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSendShow::class)
            ->name('admin.sms.send.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSendDelete::class)
            ->name('admin.sms.send.delete');
        
        // SMS 발송 액션 라우트 
        Route::post('/{id}/send', [\Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSend::class, 'send'])
            ->name('admin.sms.send.action');
        
        Route::post('/bulk-send', [\Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSend::class, 'sendBulk'])
            ->name('admin.sms.send.bulk');
        
        Route::post('/{id}/resend', [\Jiny\Admin\App\Http\Controllers\Admin\AdminSmsSend\AdminSmsSend::class, 'resend'])
            ->name('admin.sms.send.resend');
    });
});

// Admin Iptracking Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'iptracking'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminIptracking\AdminIptracking::class)
            ->name('admin.iptracking');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminIptracking\AdminIptrackingCreate::class)
            ->name('admin.iptracking.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminIptracking\AdminIptrackingEdit::class)
            ->name('admin.iptracking.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIptracking\AdminIptrackingShow::class)
            ->name('admin.iptracking.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIptracking\AdminIptrackingDelete::class)
            ->name('admin.iptracking.delete');
    });
});

// Admin Ipblacklist Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'ipblacklist'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpblacklist\AdminIpblacklist::class)
            ->name('admin.ipblacklist');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpblacklist\AdminIpblacklistCreate::class)
            ->name('admin.ipblacklist.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpblacklist\AdminIpblacklistEdit::class)
            ->name('admin.ipblacklist.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpblacklist\AdminIpblacklistShow::class)
            ->name('admin.ipblacklist.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpblacklist\AdminIpblacklistDelete::class)
            ->name('admin.ipblacklist.delete');
    });
});

// Admin Ipwhitelist Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => 'ipwhitelist'], function () {
        Route::get('/', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpwhitelist\AdminIpwhitelist::class)
            ->name('admin.ipwhitelist');
        
        Route::get('/create', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpwhitelist\AdminIpwhitelistCreate::class)
            ->name('admin.ipwhitelist.create');
        
        Route::get('/{id}/edit', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpwhitelist\AdminIpwhitelistEdit::class)
            ->name('admin.ipwhitelist.edit');
        
        Route::get('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpwhitelist\AdminIpwhitelistShow::class)
            ->name('admin.ipwhitelist.show');
        
        Route::delete('/{id}', \Jiny\Admin\App\Http\Controllers\Admin\AdminIpwhitelist\AdminIpwhitelistDelete::class)
            ->name('admin.ipwhitelist.delete');
    });
});
