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
        // 1. admin_user_types 테이블 생성
        if (!Schema::hasTable('admin_user_types')) {
            Schema::create('admin_user_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique()->comment('사용자 타입 코드');
                $table->string('name', 100)->comment('타입 이름');
                $table->text('description')->nullable()->comment('설명');
                $table->string('badge_color', 30)->default('bg-gray-100 text-gray-800')->comment('배지 색상 클래스');
                $table->json('permissions')->nullable()->comment('권한 목록 (JSON)');
                $table->json('settings')->nullable()->comment('추가 설정 (JSON)');
                $table->integer('pos')->default(0)->comment('정렬 순서');
                $table->integer('level')->default(0)->comment('권한 레벨');
                $table->boolean('enable')->default(true)->comment('활성화 상태');
                $table->integer('cnt')->default(0)->comment('사용자 수');
                $table->timestamps();
                
                $table->index('code');
                $table->index('enable');
                $table->index('pos');
            });
        }

        // 2. users 테이블에 admin 관련 컬럼 추가
        Schema::table('users', function (Blueprint $table) {
            // 기본 관리자 필드
            if (!Schema::hasColumn('users', 'isAdmin')) {
                $table->boolean('isAdmin')->default(false)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'utype')) {
                $table->string('utype', 50)->nullable()->after('isAdmin');
                $table->foreign('utype')->references('code')->on('admin_user_types')->onDelete('set null');
            }
            
            // 로그인 관련
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'login_count')) {
                $table->integer('login_count')->default(0);
            }
            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
            
            // 2FA 관련
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false);
            }
            if (!Schema::hasColumn('users', 'last_2fa_used_at')) {
                $table->timestamp('last_2fa_used_at')->nullable();
            }
            
            // 패스워드 관련
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_expires_at')) {
                $table->timestamp('password_expires_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_expiry_days')) {
                $table->integer('password_expiry_days')->nullable();
            }
            if (!Schema::hasColumn('users', 'password_expiry_notified')) {
                $table->boolean('password_expiry_notified')->default(false);
            }
            if (!Schema::hasColumn('users', 'password_must_change')) {
                $table->boolean('password_must_change')->default(false);
            }
            if (!Schema::hasColumn('users', 'force_password_change')) {
                $table->boolean('force_password_change')->default(false);
            }
            
            // 아바타
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            
            // 인덱스
            if (!Schema::hasIndex('users', 'users_utype_index')) {
                $table->index('utype');
            }
            if (!Schema::hasIndex('users', 'users_isadmin_index')) {
                $table->index('isAdmin');
            }
        });

        // 3. admin_user_logs 테이블 생성
        if (!Schema::hasTable('admin_user_logs')) {
            Schema::create('admin_user_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('event_type', 50);
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->json('extra_data')->nullable();
                $table->boolean('two_factor_used')->default(false);
                $table->string('two_factor_method', 50)->nullable();
                $table->timestamp('two_factor_verified_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'event_type']);
                $table->index('created_at');
            });
        }

        // 4. admin_password_logs 테이블 생성
        if (!Schema::hasTable('admin_password_logs')) {
            Schema::create('admin_password_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('email');
                $table->string('event_type', 50);
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->json('extra_data')->nullable();
                $table->integer('attempt_count')->default(1);
                $table->timestamp('last_attempt_at')->nullable();
                $table->timestamp('blocked_until')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'event_type']);
                $table->index('email');
                $table->index('blocked_until');
                $table->index('created_at');
            });
        }

        // 5. admin_user_sessions 테이블 생성
        if (!Schema::hasTable('admin_user_sessions')) {
            Schema::create('admin_user_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('session_id')->unique();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamp('last_activity_at');
                $table->boolean('is_active')->default(true);
                $table->json('extra_data')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_active']);
                $table->index('session_id');
                $table->index('last_activity_at');
            });
        }

        // 6. admin_user_password_logs 테이블 생성
        if (!Schema::hasTable('admin_user_password_logs')) {
            Schema::create('admin_user_password_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('old_password_hash')->nullable();
                $table->string('new_password_hash')->nullable();
                $table->string('changed_by')->nullable();
                $table->string('change_reason')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 테이블 삭제 (역순)
        Schema::dropIfExists('admin_user_password_logs');
        Schema::dropIfExists('admin_user_sessions');
        Schema::dropIfExists('admin_password_logs');
        Schema::dropIfExists('admin_user_logs');
        
        // users 테이블 컬럼 삭제
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['utype']);
            $table->dropColumn([
                'isAdmin', 'utype',
                'last_login_at', 'login_count', 'last_activity_at',
                'two_factor_secret', 'two_factor_recovery_codes', 
                'two_factor_confirmed_at', 'two_factor_enabled', 'last_2fa_used_at',
                'password_changed_at', 'password_expires_at', 'password_expiry_days',
                'password_expiry_notified', 'password_must_change', 'force_password_change',
                'avatar'
            ]);
        });
        
        Schema::dropIfExists('admin_user_types');
    }
};