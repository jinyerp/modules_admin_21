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
        Schema::create('admin_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('enable')->default(true);
            $table->timestamps();
        });

        // 샘플 데이터 추가
        DB::table('admin_tests')->insert([
            [
                'title' => '첫 번째 테스트 항목',
                'description' => '이것은 첫 번째 테스트 항목입니다.',
                'enable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '두 번째 테스트 항목',
                'description' => '이것은 두 번째 테스트 항목입니다.',
                'enable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '비활성화된 항목',
                'description' => '이 항목은 비활성화되어 있습니다.',
                'enable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '네 번째 테스트',
                'description' => '설명이 있는 테스트 항목입니다.',
                'enable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '다섯 번째 항목',
                'description' => null,
                'enable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_tests');
    }
};