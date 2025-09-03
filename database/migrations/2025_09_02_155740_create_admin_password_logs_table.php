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
        Schema::create('admin_password_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index(); // 시도한 이메일
            $table->unsignedBigInteger('user_id')->nullable()->index(); // 존재하는 사용자인 경우
            $table->string('ip_address', 45); // IP 주소
            $table->text('user_agent')->nullable(); // User Agent
            $table->string('browser')->nullable(); // 브라우저 정보
            $table->string('platform')->nullable(); // 플랫폼 정보
            $table->string('device')->nullable(); // 디바이스 종류
            $table->integer('attempt_count')->default(1); // 시도 횟수
            $table->timestamp('first_attempt_at'); // 첫 시도 시간
            $table->timestamp('last_attempt_at'); // 마지막 시도 시간
            $table->boolean('is_blocked')->default(false); // 차단 여부
            $table->timestamp('blocked_at')->nullable(); // 차단 시간
            $table->timestamp('unblocked_at')->nullable(); // 차단 해제 시간
            $table->string('status')->default('failed'); // 상태 (failed, blocked, resolved)
            $table->json('details')->nullable(); // 추가 정보
            $table->timestamps();
            
            // 인덱스
            $table->index(['email', 'ip_address']);
            $table->index(['email', 'last_attempt_at']);
            $table->index('is_blocked');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_password_logs');
    }
};
