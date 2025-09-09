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
        Schema::create('admin_sms_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('admin_sms_providers')->comment('SMS 제공업체 ID');
            $table->string('provider_name')->comment('사용된 제공업체명');
            $table->string('to_number')->comment('수신번호');
            $table->string('to_name')->nullable()->comment('수신자명');
            $table->string('from_number')->nullable()->comment('발신번호');
            $table->string('from_name')->nullable()->comment('발신자명');
            $table->text('message')->comment('메시지 내용');
            $table->integer('message_length')->comment('메시지 길이');
            $table->integer('message_count')->default(1)->comment('메시지 분할 수');
            $table->string('message_id')->nullable()->comment('제공업체 메시지 ID');
            $table->string('status')->default('pending')->comment('상태: pending, sent, delivered, failed');
            $table->string('error_code')->nullable()->comment('에러 코드');
            $table->text('error_message')->nullable()->comment('에러 메시지');
            $table->decimal('cost', 10, 4)->nullable()->comment('발송 비용');
            $table->string('currency', 3)->nullable()->comment('통화');
            $table->json('response')->nullable()->comment('API 응답 전체');
            $table->timestamp('sent_at')->nullable()->comment('발송 시간');
            $table->timestamp('delivered_at')->nullable()->comment('수신 확인 시간');
            $table->timestamp('failed_at')->nullable()->comment('실패 시간');
            $table->foreignId('sent_by')->nullable()->constrained('users')->comment('발송자 ID');
            $table->string('ip_address', 45)->nullable()->comment('발송 요청 IP');
            $table->string('user_agent')->nullable()->comment('발송 요청 User Agent');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('to_number');
            $table->index('sent_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sms_sends');
    }
};