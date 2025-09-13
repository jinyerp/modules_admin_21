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
        // 웹훅 채널 테이블
        Schema::create('admin_webhook_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('채널 이름');
            $table->enum('type', ['slack', 'discord', 'teams', 'custom'])->comment('웹훅 타입');
            $table->text('webhook_url')->comment('웹훅 URL');
            $table->text('description')->nullable()->comment('설명');
            $table->json('custom_headers')->nullable()->comment('커스텀 헤더');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            
            $table->index('name');
            $table->index('type');
            $table->index('is_active');
        });

        // 웹훅 이벤트 구독 테이블
        Schema::create('admin_webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name')->comment('채널 이름');
            $table->string('event_type')->comment('이벤트 타입');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();
            
            $table->unique(['channel_name', 'event_type']);
            $table->index('channel_name');
            $table->index('event_type');
            $table->index('is_active');
        });

        // 웹훅 발송 로그 테이블
        Schema::create('admin_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name')->comment('채널 이름');
            $table->text('message')->comment('메시지');
            $table->enum('status', ['sent', 'failed'])->comment('상태');
            $table->text('error_message')->nullable()->comment('에러 메시지');
            $table->timestamp('sent_at')->nullable()->comment('발송 시각');
            $table->timestamps();
            
            $table->index('channel_name');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_webhook_logs');
        Schema::dropIfExists('admin_webhook_subscriptions');
        Schema::dropIfExists('admin_webhook_channels');
    }
};