<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 데이터베이스와 마이그레이션 파일 간 누락된 컬럼 추가
     */
    public function up(): void
    {
        // 1. admin_user_types 테이블 - 누락된 컬럼 추가
        Schema::table('admin_user_types', function (Blueprint $table) {
            // 실제 DB에는 user_count가 있지만 cnt로 이름이 다름
            if (!Schema::hasColumn('admin_user_types', 'badge_color')) {
                $table->string('badge_color', 30)->default('bg-gray-100 text-gray-800')->after('description');
            }
            if (!Schema::hasColumn('admin_user_types', 'permissions')) {
                $table->json('permissions')->nullable()->after('badge_color');
            }
            if (!Schema::hasColumn('admin_user_types', 'settings')) {
                $table->json('settings')->nullable()->after('permissions');
            }
            // user_count와 cnt 통일 필요 (DB에는 user_count로 존재)
            if (!Schema::hasColumn('admin_user_types', 'cnt') && Schema::hasColumn('admin_user_types', 'user_count')) {
                // user_count를 cnt로 이름 변경
                $table->renameColumn('user_count', 'cnt');
            }
        });

        // 2. users 테이블 - avatar_original_name 컬럼 (DB에 있지만 마이그레이션에 없음)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_original_name')) {
                $table->string('avatar_original_name')->nullable()->after('avatar');
            }
        });

        // 3. admin_user_logs 테이블 - 실제 DB와 마이그레이션 차이
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // DB에는 있지만 마이그레이션에 없는 컬럼들
            if (!Schema::hasColumn('admin_user_logs', 'email')) {
                $table->string('email')->after('user_id');
            }
            if (!Schema::hasColumn('admin_user_logs', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('admin_user_logs', 'action')) {
                $table->string('action')->after('name');
            }
            if (!Schema::hasColumn('admin_user_logs', 'details')) {
                $table->text('details')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('admin_user_logs', 'session_id')) {
                $table->string('session_id')->nullable()->after('details');
            }
            if (!Schema::hasColumn('admin_user_logs', 'logged_at')) {
                $table->timestamp('logged_at')->after('session_id');
            }
            if (!Schema::hasColumn('admin_user_logs', 'two_factor_required')) {
                $table->boolean('two_factor_required')->default(false)->after('two_factor_method');
            }
            if (!Schema::hasColumn('admin_user_logs', 'two_factor_attempts')) {
                $table->integer('two_factor_attempts')->default(0)->after('two_factor_verified_at');
            }
            
            // 마이그레이션에는 event_type이지만 DB에는 action
            if (Schema::hasColumn('admin_user_logs', 'event_type') && !Schema::hasColumn('admin_user_logs', 'action')) {
                $table->renameColumn('event_type', 'action');
            }
            
            // extra_data를 details로 변경
            if (Schema::hasColumn('admin_user_logs', 'extra_data') && !Schema::hasColumn('admin_user_logs', 'details')) {
                $table->renameColumn('extra_data', 'details');
            }
        });

        // 4. admin_password_logs 테이블 - 추가 컬럼들
        Schema::table('admin_password_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_password_logs', 'browser')) {
                $table->string('browser')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('admin_password_logs', 'platform')) {
                $table->string('platform')->nullable()->after('browser');
            }
            if (!Schema::hasColumn('admin_password_logs', 'device')) {
                $table->string('device')->nullable()->after('platform');
            }
            if (!Schema::hasColumn('admin_password_logs', 'first_attempt_at')) {
                $table->timestamp('first_attempt_at')->nullable()->after('attempt_count');
            }
            if (!Schema::hasColumn('admin_password_logs', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('last_attempt_at');
            }
            if (!Schema::hasColumn('admin_password_logs', 'blocked_at')) {
                $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            }
            if (!Schema::hasColumn('admin_password_logs', 'unblocked_at')) {
                $table->timestamp('unblocked_at')->nullable()->after('blocked_at');
            }
            if (!Schema::hasColumn('admin_password_logs', 'status')) {
                $table->string('status')->default('failed')->after('unblocked_at');
            }
            if (!Schema::hasColumn('admin_password_logs', 'details')) {
                $table->text('details')->nullable()->after('status');
            }
            if (!Schema::hasColumn('admin_password_logs', 'action')) {
                $table->string('action')->default('failed_login')->after('updated_at');
            }
            if (!Schema::hasColumn('admin_password_logs', 'old_password_hash')) {
                $table->string('old_password_hash')->nullable()->after('action');
            }
            if (!Schema::hasColumn('admin_password_logs', 'metadata')) {
                $table->text('metadata')->nullable()->after('old_password_hash');
            }
            
            // blocked_until을 blocked_at으로 변경
            if (Schema::hasColumn('admin_password_logs', 'blocked_until') && !Schema::hasColumn('admin_password_logs', 'blocked_at')) {
                $table->renameColumn('blocked_until', 'blocked_at');
            }
        });

        // 5. admin_user_sessions 테이블 - 추가 컬럼들
        Schema::table('admin_user_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_user_sessions', 'last_activity')) {
                $table->timestamp('last_activity')->after('user_agent');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'login_at')) {
                $table->timestamp('login_at')->nullable()->after('last_activity');
            }
            if (!Schema::hasColumn('admin_user_sessions', 'browser')) {
                $table->string('browser')->nullable()->after('is_active');
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
            
            // last_activity_at을 last_activity로 변경
            if (Schema::hasColumn('admin_user_sessions', 'last_activity_at') && !Schema::hasColumn('admin_user_sessions', 'last_activity')) {
                $table->renameColumn('last_activity_at', 'last_activity');
            }
        });

        // 6. admin_user_password_logs 테이블 - 실제 DB 구조에 맞춤
        Schema::table('admin_user_password_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_user_password_logs', 'action')) {
                $table->string('action')->after('user_id');
            }
            if (!Schema::hasColumn('admin_user_password_logs', 'description')) {
                $table->text('description')->nullable()->after('action');
            }
            if (!Schema::hasColumn('admin_user_password_logs', 'performed_by')) {
                $table->unsignedBigInteger('performed_by')->nullable()->after('description');
            }
            
            // 불필요한 컬럼 제거
            if (Schema::hasColumn('admin_user_password_logs', 'old_password_hash')) {
                $table->dropColumn('old_password_hash');
            }
            if (Schema::hasColumn('admin_user_password_logs', 'new_password_hash')) {
                $table->dropColumn('new_password_hash');
            }
            if (Schema::hasColumn('admin_user_password_logs', 'changed_by')) {
                $table->dropColumn('changed_by');
            }
            if (Schema::hasColumn('admin_user_password_logs', 'change_reason')) {
                $table->dropColumn('change_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 변경사항 되돌리기는 복잡하므로 구현하지 않음
        // 필요시 수동으로 처리
    }
};