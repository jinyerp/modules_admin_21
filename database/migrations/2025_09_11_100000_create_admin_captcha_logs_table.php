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
        Schema::create('admin_captcha_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('action')->nullable(); // login, register, contact, etc.
            $table->string('provider', 50); // recaptcha, hcaptcha, etc.
            $table->boolean('success');
            $table->decimal('score', 3, 2)->nullable(); // 0.00 to 1.00 for reCAPTCHA v3
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->boolean('suspicious')->default(false);
            $table->boolean('blocked')->default(false);
            $table->string('session_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('ip_address');
            $table->index('created_at');
            $table->index(['ip_address', 'created_at']);
            $table->index('suspicious');
            $table->index('blocked');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_captcha_logs');
    }
};