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
            // 비밀번호 변경 일시
            $table->timestamp('password_changed_at')->nullable()->after('password');
            
            // 비밀번호 만료 일시
            $table->timestamp('password_expires_at')->nullable()->after('password_changed_at');
            
            // 비밀번호 만료 일수 설정 (설정 파일의 기본값과 다를 경우 사용)
            $table->integer('password_expiry_days')->nullable()->after('password_expires_at');
            
            // 비밀번호 만료 알림 상태
            $table->boolean('password_expiry_notified')->default(false)->after('password_expiry_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_changed_at',
                'password_expires_at',
                'password_expiry_days',
                'password_expiry_notified'
            ]);
        });
    }
};
