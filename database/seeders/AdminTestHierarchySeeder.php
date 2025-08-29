<?php

namespace Jiny\Admin2\Database\Seeders;

use Illuminate\Database\Seeder;
use Jiny\Admin2\App\Models\AdminTest;

class AdminTestHierarchySeeder extends Seeder
{
    /**
     * 계층 구조 테스트 데이터 생성
     *
     * @return void
     */
    public function run()
    {
        // 루트 항목 1
        $root1 = AdminTest::create([
            'title' => '프로젝트 관리',
            'description' => '전체 프로젝트 관리 시스템',
            'enable' => true,
            'parent_id' => null,
            'path' => '/1',
            'depth' => 0,
            'position' => 0,
            'is_leaf' => false,
            'children_count' => 3,
            'descendants_count' => 7
        ]);

        // 루트 1의 자식들
        $child1_1 = AdminTest::create([
            'title' => '개발팀',
            'description' => '개발팀 프로젝트',
            'enable' => true,
            'parent_id' => $root1->id,
            'path' => '/1/2',
            'depth' => 1,
            'position' => 0,
            'is_leaf' => false,
            'children_count' => 2,
            'descendants_count' => 2
        ]);

        $child1_2 = AdminTest::create([
            'title' => '디자인팀',
            'description' => '디자인팀 프로젝트',
            'enable' => true,
            'parent_id' => $root1->id,
            'path' => '/1/3',
            'depth' => 1,
            'position' => 1,
            'is_leaf' => false,
            'children_count' => 1,
            'descendants_count' => 1
        ]);

        $child1_3 = AdminTest::create([
            'title' => '마케팅팀',
            'description' => '마케팅팀 프로젝트',
            'enable' => true,
            'parent_id' => $root1->id,
            'path' => '/1/4',
            'depth' => 1,
            'position' => 2,
            'is_leaf' => false,
            'children_count' => 1,
            'descendants_count' => 1
        ]);

        // 개발팀의 자식들
        AdminTest::create([
            'title' => '백엔드 개발',
            'description' => 'API 및 서버 개발',
            'enable' => true,
            'parent_id' => $child1_1->id,
            'path' => '/1/2/5',
            'depth' => 2,
            'position' => 0,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        AdminTest::create([
            'title' => '프론트엔드 개발',
            'description' => 'UI/UX 개발',
            'enable' => true,
            'parent_id' => $child1_1->id,
            'path' => '/1/2/6',
            'depth' => 2,
            'position' => 1,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        // 디자인팀의 자식
        AdminTest::create([
            'title' => 'UI 디자인',
            'description' => '사용자 인터페이스 디자인',
            'enable' => true,
            'parent_id' => $child1_2->id,
            'path' => '/1/3/7',
            'depth' => 2,
            'position' => 0,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        // 마케팅팀의 자식
        AdminTest::create([
            'title' => '온라인 마케팅',
            'description' => '디지털 마케팅 캠페인',
            'enable' => true,
            'parent_id' => $child1_3->id,
            'path' => '/1/4/8',
            'depth' => 2,
            'position' => 0,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        // 루트 항목 2
        $root2 = AdminTest::create([
            'title' => '회사 정책',
            'description' => '회사 내부 정책 문서',
            'enable' => true,
            'parent_id' => null,
            'path' => '/9',
            'depth' => 0,
            'position' => 1,
            'is_leaf' => false,
            'children_count' => 2,
            'descendants_count' => 2
        ]);

        // 루트 2의 자식들
        AdminTest::create([
            'title' => '인사 정책',
            'description' => '인사 관련 규정',
            'enable' => true,
            'parent_id' => $root2->id,
            'path' => '/9/10',
            'depth' => 1,
            'position' => 0,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        AdminTest::create([
            'title' => '보안 정책',
            'description' => '정보 보안 규정',
            'enable' => false,
            'parent_id' => $root2->id,
            'path' => '/9/11',
            'depth' => 1,
            'position' => 1,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        // 루트 항목 3 (자식 없음)
        AdminTest::create([
            'title' => '임시 문서',
            'description' => '임시로 저장된 문서',
            'enable' => false,
            'parent_id' => null,
            'path' => '/12',
            'depth' => 0,
            'position' => 2,
            'is_leaf' => true,
            'children_count' => 0,
            'descendants_count' => 0
        ]);

        $this->command->info('계층 구조 테스트 데이터가 생성되었습니다.');
    }
}