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
        Schema::create('unlock_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('token', 255)->unique(); // SHA256 해시 저장
            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expired_at')->nullable(); // 수동 만료 처리
            $table->timestamps();
            
            // 외래 키 제약
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 인덱스
            $table->index(['user_id', 'expires_at']);
            $table->index(['token', 'used_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlock_tokens');
    }
};