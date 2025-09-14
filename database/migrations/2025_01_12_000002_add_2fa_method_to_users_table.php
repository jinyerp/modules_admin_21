<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 2FA 방법 추가 (totp, sms, email)
            $table->string('two_factor_method', 20)->default('totp')->after('two_factor_enabled');
            
            // SMS 2FA를 위한 전화번호 (없을 수도 있음)
            $table->string('phone_number')->nullable()->after('email');
            $table->boolean('phone_verified')->default(false)->after('phone_number');
            
            // 백업 코드 사용 기록
            $table->json('used_backup_codes')->nullable()->after('two_factor_recovery_codes');
            
            // 마지막 코드 발송 시간 (재발송 제한용)
            $table->timestamp('last_code_sent_at')->nullable()->after('last_2fa_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_method',
                'phone_number',
                'phone_verified',
                'used_backup_codes',
                'last_code_sent_at'
            ]);
        });
    }
};