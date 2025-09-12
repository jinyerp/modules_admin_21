<?php

use Illuminate\Support\Facades\Route;
use Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa\Admin2FAController;

/**
 * 2FA 관리 라우트
 */
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
    
    // 2FA 상태 확인
    Route::get('/status', [Admin2FAController::class, 'getStatus'])
        ->name('admin.user.2fa.status');
    
    // 전화번호 업데이트
    Route::post('/update-phone', [Admin2FAController::class, 'updatePhoneNumber'])
        ->name('admin.user.profile.update-phone');
});

// 만료된 코드 정리 (크론잡용)
Route::get('/admin/2fa/cleanup', [Admin2FAController::class, 'cleanupExpiredCodes'])
    ->name('admin.2fa.cleanup')
    ->middleware(['api']);