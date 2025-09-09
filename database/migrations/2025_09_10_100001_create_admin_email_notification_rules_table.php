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
        Schema::create('admin_email_notification_rules', function (Blueprint $table) {
            $table->id();
            
            // 규칙 기본 정보
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            
            // 이벤트 조건
            $table->string('event_type'); // login_failed, password_reset, ip_blocked 등
            $table->json('conditions')->nullable(); // 추가 조건 (예: failed_attempts > 3)
            
            // 수신자 설정
            $table->enum('recipient_type', ['user', 'admin', 'custom', 'role'])->default('user');
            $table->json('recipients')->nullable(); // 수신자 목록 또는 역할
            $table->boolean('notify_user')->default(true); // 사용자에게 알림
            $table->boolean('notify_admins')->default(false); // 관리자에게 알림
            
            // 템플릿 설정
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('template_slug')->nullable();
            
            // 발송 제한
            $table->integer('throttle_minutes')->nullable(); // 동일 이벤트 재발송 제한 시간
            $table->integer('max_per_day')->nullable(); // 일일 최대 발송 수
            $table->integer('max_per_hour')->nullable(); // 시간당 최대 발송 수
            
            // 우선순위 및 지연
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->integer('delay_seconds')->default(0); // 발송 지연 시간
            
            // 활성 시간대
            $table->time('active_from')->nullable(); // 활성 시작 시간
            $table->time('active_to')->nullable(); // 활성 종료 시간
            $table->json('active_days')->nullable(); // 활성 요일 [1,2,3,4,5] (월-금)
            
            // 통계
            $table->integer('sent_count')->default(0);
            $table->timestamp('last_sent_at')->nullable();
            
            $table->timestamps();
            
            // 인덱스
            $table->index('event_type');
            $table->index('is_active');
            $table->index(['event_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_notification_rules');
    }
};