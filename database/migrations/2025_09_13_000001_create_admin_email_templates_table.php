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
        Schema::create('admin_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->enum('type', ['html', 'text', 'markdown'])->default('html');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('status')->default(true); // is_active와 동일한 역할
            $table->integer('priority')->default(0);
            $table->json('attachments')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index('slug');
            $table->index('is_active');
            $table->index('category');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_templates');
    }
};