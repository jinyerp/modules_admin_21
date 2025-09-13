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
        // 푸시 구독 테이블
        Schema::create('admin_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('사용자 ID');
            $table->enum('type', ['web', 'mobile'])->comment('푸시 타입');
            $table->text('endpoint')->comment('엔드포인트/토큰');
            $table->json('auth_keys')->nullable()->comment('인증 키 (Web Push용)');
            $table->json('device_info')->nullable()->comment('디바이스 정보');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index('is_active');
        });

        // 푸시 발송 로그 테이블
        Schema::create('admin_push_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('사용자 ID');
            $table->enum('type', ['web', 'mobile'])->comment('푸시 타입');
            $table->string('title')->comment('제목');
            $table->enum('status', ['sent', 'failed'])->comment('상태');
            $table->text('error_message')->nullable()->comment('에러 메시지');
            $table->timestamp('sent_at')->nullable()->comment('발송 시각');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('sent_at');
        });

        // 알림 채널 설정 테이블
        Schema::create('admin_notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->comment('이벤트 타입');
            $table->string('channel')->comment('채널 (email, sms, webhook, push)');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            
            $table->unique(['event_type', 'channel']);
            $table->index('event_type');
            $table->index('channel');
            $table->index('is_active');
        });

        // 멀티채널 알림 로그 테이블
        Schema::create('admin_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->comment('이벤트 타입');
            $table->json('channels')->comment('발송 채널');
            $table->json('results')->comment('발송 결과');
            $table->json('data')->nullable()->comment('데이터');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('사용자 ID');
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notification_logs');
        Schema::dropIfExists('admin_notification_channels');
        Schema::dropIfExists('admin_push_logs');
        Schema::dropIfExists('admin_push_subscriptions');
    }
};