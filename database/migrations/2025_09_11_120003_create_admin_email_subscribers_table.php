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
        Schema::create('admin_email_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->json('segments')->nullable(); // Array of segment tags
            $table->boolean('is_subscribed')->default(true);
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason')->nullable();
            $table->boolean('is_bounced')->default(false);
            $table->timestamp('bounced_at')->nullable();
            $table->string('bounce_type')->nullable();
            $table->boolean('is_complained')->default(false);
            $table->timestamp('complained_at')->nullable();
            $table->json('preferences')->nullable(); // Email preferences
            $table->string('language', 5)->default('en');
            $table->string('timezone')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('source')->nullable(); // signup, import, api, etc.
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('is_subscribed');
            $table->index('is_bounced');
            $table->index('is_complained');
            $table->index('created_at');
            $table->fullText('segments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_email_subscribers');
    }
};