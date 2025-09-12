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
        Schema::create('admin_captcha_ip_lists', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->enum('list_type', ['whitelist', 'blacklist']);
            $table->string('reason')->nullable();
            $table->string('added_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['ip_address', 'list_type']);
            $table->index('list_type');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_captcha_ip_lists');
    }
};