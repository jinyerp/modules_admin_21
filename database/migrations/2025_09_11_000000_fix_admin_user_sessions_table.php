<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admin_user_sessions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('admin_user_sessions', 'login_at')) {
                $table->timestamp('login_at')->nullable()->after('last_activity_at');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'browser')) {
                $table->string('browser')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'browser_version')) {
                $table->string('browser_version')->nullable()->after('browser');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'platform')) {
                $table->string('platform')->nullable()->after('browser_version');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'device')) {
                $table->string('device')->nullable()->after('platform');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'two_factor_used')) {
                $table->boolean('two_factor_used')->default(false)->after('device');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'payload')) {
                $table->text('payload')->nullable()->after('two_factor_used');
            }
        });
    }

    public function down()
    {
        Schema::table('admin_user_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'login_at',
                'browser',
                'browser_version',
                'platform',
                'device',
                'two_factor_used',
                'payload'
            ]);
        });
    }
};