<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 계층적 구조를 위한 컬럼 추가
     * 
     * 세 가지 방식을 모두 지원하는 하이브리드 구조:
     * 1. Adjacency List (부모-자식 관계): parent_id
     * 2. Nested Set Model (효율적인 조회): left, right
     * 3. Path Enumeration (경로 저장): path
     * 4. Closure Table 지원을 위한 depth
     */
    public function up(): void
    {
        Schema::table('admin_tests', function (Blueprint $table) {
            // 1. Adjacency List 패턴 (가장 간단하고 직관적)
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('admin_tests')->onDelete('cascade');
            
            // 2. Nested Set Model 패턴 (조회 성능 최적화)
            // 전체 트리 조회, 서브트리 조회가 빠름
            $table->unsignedInteger('_lft')->default(0)->after('parent_id');
            $table->unsignedInteger('_rgt')->default(0)->after('_lft');
            
            // 3. Path Enumeration 패턴 (경로 기반)
            // 조상/자손 관계를 빠르게 파악 가능
            $table->string('path', 500)->nullable()->after('_rgt');
            
            // 4. 추가 계층 정보
            $table->unsignedInteger('depth')->default(0)->after('path'); // 깊이 (루트는 0)
            $table->unsignedInteger('position')->default(0)->after('depth'); // 같은 부모 내 순서
            
            // 5. 계층 관련 메타 정보
            $table->boolean('is_leaf')->default(true)->after('position'); // 말단 노드 여부
            $table->unsignedInteger('children_count')->default(0)->after('is_leaf'); // 직계 자식 수
            $table->unsignedInteger('descendants_count')->default(0)->after('children_count'); // 모든 자손 수
            
            // 인덱스 추가 (성능 최적화)
            $table->index('parent_id', 'idx_parent');
            $table->index(['_lft', '_rgt'], 'idx_nested_set');
            $table->index('path', 'idx_path');
            $table->index('depth', 'idx_depth');
            $table->index(['parent_id', 'position'], 'idx_parent_position');
        });

        // 기존 데이터를 계층 구조로 업데이트
        $this->createHierarchicalSampleData();
    }

    /**
     * 계층적 샘플 데이터 생성
     */
    private function createHierarchicalSampleData()
    {
        // 기존 데이터 가져오기
        $items = DB::table('admin_tests')->get();
        
        if ($items->count() >= 5) {
            // 첫 번째 항목을 루트로 설정
            DB::table('admin_tests')
                ->where('id', $items[0]->id)
                ->update([
                    'parent_id' => null,
                    'path' => '/' . $items[0]->id,
                    'depth' => 0,
                    'position' => 1,
                    '_lft' => 1,
                    '_rgt' => 10,
                    'is_leaf' => false,
                    'children_count' => 2,
                    'descendants_count' => 4
                ]);
            
            // 두 번째 항목을 첫 번째의 자식으로
            DB::table('admin_tests')
                ->where('id', $items[1]->id)
                ->update([
                    'parent_id' => $items[0]->id,
                    'path' => '/' . $items[0]->id . '/' . $items[1]->id,
                    'depth' => 1,
                    'position' => 1,
                    '_lft' => 2,
                    '_rgt' => 5,
                    'is_leaf' => false,
                    'children_count' => 1,
                    'descendants_count' => 1
                ]);
            
            // 세 번째 항목을 첫 번째의 자식으로
            DB::table('admin_tests')
                ->where('id', $items[2]->id)
                ->update([
                    'parent_id' => $items[0]->id,
                    'path' => '/' . $items[0]->id . '/' . $items[2]->id,
                    'depth' => 1,
                    'position' => 2,
                    '_lft' => 6,
                    '_rgt' => 9,
                    'is_leaf' => false,
                    'children_count' => 1,
                    'descendants_count' => 1
                ]);
            
            // 네 번째 항목을 두 번째의 자식으로
            DB::table('admin_tests')
                ->where('id', $items[3]->id)
                ->update([
                    'parent_id' => $items[1]->id,
                    'path' => '/' . $items[0]->id . '/' . $items[1]->id . '/' . $items[3]->id,
                    'depth' => 2,
                    'position' => 1,
                    '_lft' => 3,
                    '_rgt' => 4,
                    'is_leaf' => true,
                    'children_count' => 0,
                    'descendants_count' => 0
                ]);
            
            // 다섯 번째 항목을 세 번째의 자식으로
            DB::table('admin_tests')
                ->where('id', $items[4]->id)
                ->update([
                    'parent_id' => $items[2]->id,
                    'path' => '/' . $items[0]->id . '/' . $items[2]->id . '/' . $items[4]->id,
                    'depth' => 2,
                    'position' => 1,
                    '_lft' => 7,
                    '_rgt' => 8,
                    'is_leaf' => true,
                    'children_count' => 0,
                    'descendants_count' => 0
                ]);
        }
        
        // 추가 계층 데이터 삽입
        DB::table('admin_tests')->insert([
            [
                'title' => '독립 루트 노드',
                'description' => '다른 계층 구조의 루트입니다.',
                'enable' => true,
                'parent_id' => null,
                'path' => '/6',
                'depth' => 0,
                'position' => 2,
                '_lft' => 11,
                '_rgt' => 16,
                'is_leaf' => false,
                'children_count' => 2,
                'descendants_count' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '독립 루트의 자식 1',
                'description' => '독립 루트 노드의 첫 번째 자식',
                'enable' => true,
                'parent_id' => 6, // 위에서 삽입될 ID
                'path' => '/6/7',
                'depth' => 1,
                'position' => 1,
                '_lft' => 12,
                '_rgt' => 13,
                'is_leaf' => true,
                'children_count' => 0,
                'descendants_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '독립 루트의 자식 2',
                'description' => '독립 루트 노드의 두 번째 자식',
                'enable' => true,
                'parent_id' => 6,
                'path' => '/6/8',
                'depth' => 1,
                'position' => 2,
                '_lft' => 14,
                '_rgt' => 15,
                'is_leaf' => true,
                'children_count' => 0,
                'descendants_count' => 0,
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
        Schema::table('admin_tests', function (Blueprint $table) {
            // 인덱스 제거
            $table->dropIndex('idx_parent');
            $table->dropIndex('idx_nested_set');
            $table->dropIndex('idx_path');
            $table->dropIndex('idx_depth');
            $table->dropIndex('idx_parent_position');
            
            // 외래키 제약 제거
            $table->dropForeign(['parent_id']);
            
            // 컬럼 제거
            $table->dropColumn([
                'parent_id',
                '_lft',
                '_rgt',
                'path',
                'depth',
                'position',
                'is_leaf',
                'children_count',
                'descendants_count'
            ]);
        });
    }
};