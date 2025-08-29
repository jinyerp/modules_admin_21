<?php

namespace Jiny\Admin2\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AdminTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ko_KR');
        
        $data = [];
        
        // 50개의 샘플 데이터 생성
        for ($i = 1; $i <= 50; $i++) {
            $data[] = [
                'title' => $faker->randomElement(['테스트', '샘플', '데모', '예제', '항목']) . ' ' . $i . ' - ' . $faker->company(),
                'description' => $faker->randomElement([
                    $faker->realText(200),
                    $faker->paragraph(3),
                    $faker->sentence(10),
                    null // 일부는 설명이 없음
                ]),
                'enable' => $faker->boolean(70), // 70% 확률로 활성화
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ];
        }
        
        // 특정 테스트용 데이터 추가
        $data[] = [
            'title' => '필터 테스트용 - 활성화된 항목',
            'description' => '이 항목은 활성화되어 있으며 필터 테스트용입니다.',
            'enable' => true,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(5),
        ];
        
        $data[] = [
            'title' => '필터 테스트용 - 비활성화된 항목',
            'description' => '이 항목은 비활성화되어 있으며 필터 테스트용입니다.',
            'enable' => false,
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(15),
        ];
        
        $data[] = [
            'title' => '정렬 테스트용 - AAA로 시작하는 제목',
            'description' => '알파벳 순서로 정렬할 때 상단에 표시되어야 합니다.',
            'enable' => true,
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(25),
        ];
        
        $data[] = [
            'title' => '정렬 테스트용 - ZZZ로 시작하는 제목',
            'description' => '알파벳 순서로 정렬할 때 하단에 표시되어야 합니다.',
            'enable' => true,
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(35),
        ];
        
        // 검색 테스트용 데이터
        $data[] = [
            'title' => '검색어 Laravel을 포함하는 제목',
            'description' => 'Laravel 프레임워크를 사용한 프로젝트입니다. PHP와 함께 사용됩니다.',
            'enable' => true,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(2),
        ];
        
        $data[] = [
            'title' => '특수문자 테스트 @#$%^&*()',
            'description' => '특수문자가 포함된 제목과 설명입니다. !@#$%^&*()',
            'enable' => true,
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(3),
        ];
        
        // 긴 텍스트 테스트용
        $data[] = [
            'title' => '매우 긴 제목을 가진 항목입니다. ' . str_repeat('긴 텍스트 ', 20),
            'description' => str_repeat('이것은 매우 긴 설명입니다. ', 50),
            'enable' => true,
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(10),
        ];
        
        // 최신 데이터
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'title' => "오늘 생성된 최신 항목 {$i}",
                'description' => "방금 추가된 최신 데이터입니다. 생성 시간: " . now()->format('Y-m-d H:i:s'),
                'enable' => true,
                'created_at' => now()->subMinutes($i * 10),
                'updated_at' => now()->subMinutes($i * 10),
            ];
        }
        
        // 데이터베이스에 삽입
        DB::table('admin_tests')->insert($data);
        
        $this->command->info('AdminTest 샘플 데이터 ' . count($data) . '개가 추가되었습니다.');
    }
}