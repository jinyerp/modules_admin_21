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
        Schema::create('admin_ip_whitelist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique(); // IPv4/IPv6 지원
            $table->string('description')->nullable(); // IP 설명 (예: "본사 사무실")
            $table->string('type', 20)->default('single'); // single, range, cidr
            $table->string('ip_range_start', 45)->nullable(); // IP 범위 시작
            $table->string('ip_range_end', 45)->nullable(); // IP 범위 끝
            $table->integer('cidr_prefix')->nullable(); // CIDR 표기법용
            $table->boolean('is_active')->default(true); // 활성화 여부
            $table->string('added_by')->nullable(); // 추가한 관리자
            $table->datetime('expires_at')->nullable(); // 만료일 (임시 허용용)
            $table->integer('access_count')->default(0); // 접근 횟수
            $table->datetime('last_accessed_at')->nullable(); // 마지막 접근 시간
            $table->json('metadata')->nullable(); // 추가 정보
            $table->timestamps();
            
            // 인덱스
            $table->index('is_active');
            $table->index('expires_at');
            $table->index(['ip_range_start', 'ip_range_end']);
        });

        // IP 접근 로그 테이블
        Schema::create('admin_ip_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_allowed'); // 허용/차단 여부
            $table->string('reason')->nullable(); // 차단 사유
            $table->json('request_data')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index('ip_address');
            $table->index('is_allowed');
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ip_access_logs');
        Schema::dropIfExists('admin_ip_whitelist');
    }
};