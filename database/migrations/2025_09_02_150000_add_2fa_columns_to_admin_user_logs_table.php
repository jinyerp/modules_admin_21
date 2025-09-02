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
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // 2FA 관련 컬럼 추가
            $table->boolean('two_factor_used')->default(false)->after('status')->comment('2FA 사용 여부');
            $table->enum('two_factor_method', ['app', 'backup', 'none'])->default('none')->after('two_factor_used')->comment('2FA 인증 방법');
            $table->boolean('two_factor_required')->default(false)->after('two_factor_method')->comment('2FA 필수 여부');
            $table->timestamp('two_factor_verified_at')->nullable()->after('two_factor_required')->comment('2FA 인증 시간');
            $table->integer('two_factor_attempts')->default(0)->after('two_factor_verified_at')->comment('2FA 시도 횟수');
            
            // 인덱스 추가
            $table->index('two_factor_used');
            $table->index('two_factor_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // 인덱스 제거
            $table->dropIndex(['two_factor_used']);
            $table->dropIndex(['two_factor_method']);
            
            // 컬럼 제거
            $table->dropColumn([
                'two_factor_used',
                'two_factor_method',
                'two_factor_required',
                'two_factor_verified_at',
                'two_factor_attempts'
            ]);
        });
    }
};