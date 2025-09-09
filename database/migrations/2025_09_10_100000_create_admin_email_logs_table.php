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
        Schema::create('admin_email_logs', function (Blueprint $table) {
            $table->id();
            
            // 템플릿 정보
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('template_slug')->nullable();
            
            // 수신자 정보
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('cc_emails')->nullable();
            $table->string('bcc_emails')->nullable();
            
            // 발신자 정보
            $table->string('from_email');
            $table->string('from_name')->nullable();
            
            // 메일 내용
            $table->string('subject');
            $table->longText('body');
            $table->json('variables')->nullable(); // 사용된 변수들
            $table->json('attachments')->nullable(); // 첨부파일 정보
            
            // 발송 상태
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('opened_at')->nullable(); // 오픈 추적
            $table->timestamp('clicked_at')->nullable(); // 클릭 추적
            
            // 메타 정보
            $table->string('event_type')->nullable(); // 이벤트 타입 (login_failed, password_reset 등)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // 발송 대상 사용자
            $table->unsignedBigInteger('triggered_by')->nullable(); // 발송을 트리거한 사용자
            
            // 우선순위 및 큐
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->string('queue_name')->nullable();
            $table->string('job_id')->nullable();
            
            $table->timestamps();
            
            // 인덱스
            $table->index('to_email');
            $table->index('status');
            $table->index('event_type');
            $table->index('created_at');
            $table->index(['template_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_logs');
    }
};