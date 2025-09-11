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
        // SQLite는 인덱스와 컬럼 변경이 제한적이므로 테이블을 재생성
        Schema::dropIfExists('admin_user_logs_backup');
        
        // 기존 데이터 백업 (있다면)
        if (Schema::hasTable('admin_user_logs')) {
            Schema::rename('admin_user_logs', 'admin_user_logs_backup');
        }
        
        // 새 테이블 생성
        Schema::create('admin_user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('action', 100);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->boolean('two_factor_used')->default(false);
            $table->boolean('two_factor_required')->default(false);
            $table->string('two_factor_method', 50)->nullable();
            $table->timestamp('two_factor_verified_at')->nullable();
            $table->integer('two_factor_attempts')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'action']);
            $table->index('action');
            $table->index('email');
            $table->index('session_id');
        });
        
        // 백업 테이블이 있으면 데이터 이전 (가능한 컬럼만)
        if (Schema::hasTable('admin_user_logs_backup')) {
            $oldData = DB::table('admin_user_logs_backup')->get();
            foreach ($oldData as $row) {
                DB::table('admin_user_logs')->insert([
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'action' => $row->event_type ?? 'unknown',
                    'ip_address' => $row->ip_address,
                    'user_agent' => $row->user_agent,
                    'details' => $row->extra_data,
                    'two_factor_used' => $row->two_factor_used ?? false,
                    'two_factor_method' => $row->two_factor_method,
                    'two_factor_verified_at' => $row->two_factor_verified_at,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
            Schema::dropIfExists('admin_user_logs_backup');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'name', 
                'action',
                'details',
                'session_id',
                'logged_at',
                'two_factor_required',
                'two_factor_attempts'
            ]);
            
            // 원래 컬럼 복원
            $table->string('event_type', 50)->after('user_id');
            $table->json('extra_data')->nullable()->after('user_agent');
        });
    }
};