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
        Schema::create('admin_email_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->unsignedBigInteger('email_log_id')->nullable();
            $table->string('recipient_email');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('template_version_id')->nullable();
            $table->unsignedBigInteger('ab_test_id')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('opened_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->timestamp('clicked_at')->nullable();
            $table->integer('click_count')->default(0);
            $table->json('clicked_links')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->string('bounce_type')->nullable(); // hard, soft
            $table->string('bounce_reason')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('email_client')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('email_log_id')->references('id')->on('admin_email_logs')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('admin_emailtemplates')->onDelete('set null');
            
            // Indexes for performance
            $table->index('recipient_email');
            $table->index('sent_at');
            $table->index('opened_at');
            $table->index('clicked_at');
            $table->index(['template_id', 'sent_at']);
            $table->index('ab_test_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_tracking');
    }
};