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
        Schema::create('admin_sms_queues', function (Blueprint $table) {
            $table->id();
            $table->string('to', 20);
            $table->text('message');
            $table->string('provider', 50)->nullable();
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->string('batch_id')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('cost', 10, 4)->nullable();
            $table->string('message_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_at');
            $table->index('batch_id');
            $table->index(['status', 'priority', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sms_queues');
    }
};