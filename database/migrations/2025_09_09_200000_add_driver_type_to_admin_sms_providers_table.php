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
        Schema::table('admin_sms_providers', function (Blueprint $table) {
            // 드라이버 타입 추가 (vonage, twilio 등)
            $table->string('driver_type', 50)->default('vonage')->after('provider_name');
            
            // Twilio 설정 필드 추가
            $table->string('account_sid')->nullable()->after('api_secret');
            $table->string('auth_token')->nullable()->after('account_sid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_sms_providers', function (Blueprint $table) {
            $table->dropColumn(['driver_type', 'account_sid', 'auth_token']);
        });
    }
};