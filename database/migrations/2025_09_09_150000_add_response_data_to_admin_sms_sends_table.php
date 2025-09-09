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
        Schema::table('admin_sms_sends', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_sms_sends', 'response_data')) {
                $table->text('response_data')->nullable()->after('error_message');
            }
            if (!Schema::hasColumn('admin_sms_sends', 'retry_count')) {
                $table->integer('retry_count')->default(0)->after('response_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_sms_sends', function (Blueprint $table) {
            $table->dropColumn(['response_data', 'retry_count']);
        });
    }
};