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
        // IP별 로그인 시도 기록
        Schema::create('admin_ip_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique(); // IPv4/IPv6 지원
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_until')->nullable();
            $table->json('metadata')->nullable(); // 추가 정보 (user agent, referer 등)
            $table->timestamps();
            
            $table->index('ip_address');
            $table->index('is_blocked');
            $table->index('blocked_until');
            $table->index('last_attempt_at');
        });
        
        // IP 블랙리스트
        Schema::create('admin_ip_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('ip_range')->nullable(); // CIDR 표기법 지원
            $table->string('reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('added_by')->nullable(); // 추가한 관리자
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('ip_address');
            $table->index('is_active');
            $table->index('expires_at');
        });
        
        // IP 화이트리스트
        Schema::create('admin_ip_whitelist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('ip_range')->nullable(); // CIDR 표기법 지원
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('added_by')->nullable(); // 추가한 관리자
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('ip_address');
            $table->index('is_active');
            $table->index('expires_at');
        });
        
        // IP 접근 로그 (선택적 - 상세 모니터링용)
        Schema::create('admin_ip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('action'); // login_attempt, login_success, blocked, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('ip_address');
            $table->index('action');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ip_logs');
        Schema::dropIfExists('admin_ip_whitelist');
        Schema::dropIfExists('admin_ip_blacklist');
        Schema::dropIfExists('admin_ip_attempts');
    }
};