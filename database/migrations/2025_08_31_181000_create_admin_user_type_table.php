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
        Schema::create('admin_user_type', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('타입 코드');
            $table->string('name')->comment('타입 명칭');
            $table->string('description')->nullable()->comment('설명');
            $table->integer('level')->default(0)->comment('권한 레벨 (높을수록 높은 권한)');
            $table->boolean('enable')->default(true)->comment('활성화 여부');
            $table->integer('pos')->default(0)->comment('정렬 순서');
            $table->timestamps();
            
            $table->index('code');
            $table->index('enable');
            $table->index('level');
        });

        // 초기 데이터 삽입
        $this->insertInitialData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_type');
    }

    /**
     * Insert initial user type data
     */
    private function insertInitialData(): void
    {
        $now = now();
        
        DB::table('admin_user_type')->insert([
            [
                'code' => 'super',
                'name' => 'Super Admin',
                'description' => '최고 관리자 - 모든 권한을 가진 시스템 관리자',
                'level' => 100,
                'enable' => true,
                'pos' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'admin',
                'name' => 'Administrator',
                'description' => '일반 관리자 - 시스템 설정을 제외한 대부분의 권한',
                'level' => 50,
                'enable' => true,
                'pos' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'staff',
                'name' => 'Staff',
                'description' => '스태프 - 콘텐츠 관리 및 기본 운영 권한',
                'level' => 10,
                'enable' => true,
                'pos' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
};