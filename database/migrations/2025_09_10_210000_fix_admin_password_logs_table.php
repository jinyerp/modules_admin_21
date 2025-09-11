<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite는 컬럼 추가/삭제가 제한적이므로 테이블을 재생성
        Schema::dropIfExists('admin_password_logs_backup');
        
        // 기존 데이터 백업 (있다면)
        if (Schema::hasTable('admin_password_logs')) {
            Schema::rename('admin_password_logs', 'admin_password_logs_backup');
        }
        
        // 새 테이블 생성
        Schema::create('admin_password_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('action', 50)->nullable();
            $table->string('old_password_hash')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('device', 50)->nullable();
            $table->integer('attempt_count')->default(0);
            $table->timestamp('first_attempt_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('unblocked_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->json('details')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('is_blocked');
            $table->index('blocked_at');
        });
        
        // 백업 테이블이 있으면 데이터 이전 (가능한 컬럼만)
        if (Schema::hasTable('admin_password_logs_backup')) {
            $oldData = DB::table('admin_password_logs_backup')->get();
            foreach ($oldData as $row) {
                DB::table('admin_password_logs')->insert([
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'email' => $row->email,
                    'action' => $row->event_type ?? null,
                    'ip_address' => $row->ip_address,
                    'user_agent' => $row->user_agent,
                    'attempt_count' => $row->attempt_count ?? 0,
                    'last_attempt_at' => $row->last_attempt_at,
                    'is_blocked' => isset($row->blocked_until) && $row->blocked_until ? true : false,
                    'blocked_at' => $row->blocked_until,
                    'details' => $row->extra_data,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
            Schema::dropIfExists('admin_password_logs_backup');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_password_logs');
        
        // 원래 테이블 구조로 복원
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
};