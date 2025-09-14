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
            $table->foreignId('template_id')->nullable()->constrained('admin_email_templates')->onDelete('set null');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('subject');
            $table->longText('body');
            $table->enum('type', ['html', 'text', 'markdown'])->default('html');
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'bounced', 'opened', 'clicked'])->default('pending');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('attachments')->nullable();
            $table->json('variables')->nullable();
            $table->string('message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->json('clicks')->nullable(); // Array of clicked links
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type')->nullable(); // 이벤트 타입 (login_failed, password_reset 등)
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index('to_email');
            $table->index('status');
            $table->index('sent_at');
            $table->index('template_id');
            $table->index('user_id');
            $table->index('event_type');
            $table->index('created_at');
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