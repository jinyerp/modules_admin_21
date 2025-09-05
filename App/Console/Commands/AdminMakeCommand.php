<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 관리자 CRUD 모듈 생성 명령어
 * 
 * @jiny/admin 패키지의 핵심 명령어로, Laravel Artisan을 통해
 * 완전한 CRUD(Create, Read, Update, Delete) 기능을 가진
 * 관리자 모듈을 자동으로 생성합니다.
 * 
 * @package Jiny\Admin
 * @author JinyPHP
 * @since 1.0.0
 */
class AdminMakeCommand extends Command
{
    /**
     * 콘솔 명령어 시그니처
     * 
     * 사용법: php artisan admin:make {module} {feature}
     * 
     * Arguments:
     *   module : 모듈 이름 (예: shop, blog, crm)
     *   feature : 기능 이름 (예: product, category, customer)
     * 
     * Options:
     *   --with-seeder : 샘플 데이터가 포함된 시더 생성
     *   --fields : 추가 필드 정의 (예: --fields="name:string,price:decimal")
     *   --no-migration : 마이그레이션 생성 및 실행 건너뛰기
     *
     * @var string
     */
    protected $signature = 'admin:make {module : The module name} {feature : The feature name} 
                            {--with-seeder : Create a seeder with sample data}
                            {--fields= : Comma-separated list of additional fields (e.g., name:string,price:decimal)}
                            {--no-migration : Skip migration creation and execution}';

    /**
     * 콘솔 명령어 설명
     * 
     * 이 명령어는 다음을 자동으로 생성합니다:
     * - 6개의 컨트롤러 (Index, Create, Edit, Delete, Show + JSON 설정)
     * - Eloquent 모델
     * - 데이터베이스 마이그레이션
     * - 5개의 Blade 뷰 템플릿
     * - 모델 팩토리
     * - 라우트 등록
     * - 시더 (옵션)
     *
     * @var string
     */
    protected $description = 'Create a new Admin CRUD controller with all necessary files';

    /**
     * 명령어 실행 메인 메서드
     * 
     * 전체 CRUD 모듈 생성 프로세스를 조율하고 각 단계를 순서대로 실행합니다.
     * 각 단계는 독립적으로 실패할 수 있으며, 오류 발생 시 적절한 메시지를 표시합니다.
     * 
     * 실행 순서:
     * 1. 컨트롤러 생성 (6개 파일)
     * 2. 라우트 등록
     * 3. 마이그레이션 생성 (--no-migration 옵션이 없는 경우)
     * 4. Eloquent 모델 생성
     * 5. Blade 뷰 템플릿 복사
     * 6. 모델 팩토리 생성
     * 7. 시더 생성 (--with-seeder 옵션이 있는 경우)
     * 8. 마이그레이션 실행 (--no-migration 옵션이 없는 경우)
     * 
     * @return int 명령어 실행 결과 (0: 성공, 1: 실패)
     */
    public function handle()
    {
        $module = $this->argument('module');
        $feature = $this->argument('feature');

        // Convert to proper case
        $moduleStudly = Str::studly($module);
        $featureStudly = Str::studly($feature);
        $featureSnake = Str::snake($feature);
        $featurePlural = Str::plural($featureSnake);

        $this->info("Creating Admin CRUD for {$moduleStudly}::{$featureStudly}");

        // Step 1: Create Controllers
        $this->createControllers($moduleStudly, $featureStudly);

        // Step 2: Register Routes
        $this->registerRoutes($moduleStudly, $featureStudly, $featureSnake);

        // Step 3: Create Migration
        if (! $this->option('no-migration')) {
            $this->createMigration($moduleStudly, $featurePlural);
        }

        // Step 4: Create Model
        $this->createModel($moduleStudly, $featureStudly, $featurePlural);

        // Step 5: Copy View Resources
        $this->copyViewResources($moduleStudly, $featureSnake);

        // Step 6: Create Factory
        $this->createFactory($moduleStudly, $featureStudly);

        // Step 7: Create Seeder if requested
        if ($this->option('with-seeder')) {
            $this->createSeeder($moduleStudly, $featureStudly, $featurePlural);
        }

        // Step 7: Run migration
        if (! $this->option('no-migration')) {
            $this->runMigration();
        }

        $this->info("Admin CRUD for {$moduleStudly}::{$featureStudly} created successfully!");
        $this->info("Don't forget to register your module's service provider if not already done.");
    }

    /**
     * 스텁 템플릿으로부터 컨트롤러 파일들을 생성
     * 
     * 6개의 컨트롤러를 생성합니다:
     * - Admin{Feature}.php : 메인 리스트 컨트롤러
     * - Admin{Feature}Create.php : 생성 폼 컨트롤러
     * - Admin{Feature}Edit.php : 수정 폼 컨트롤러
     * - Admin{Feature}Delete.php : 삭제 처리 컨트롤러
     * - Admin{Feature}Show.php : 상세 보기 컨트롤러
     * - Admin{Feature}.json : 설정 파일 (테이블 컬럼, 폼 필드 등)
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $feature 기능 이름 (StudlyCase)
     * @return void
     */
    protected function createControllers($module, $feature)
    {
        $this->info('Creating controllers...');

        $controllerPath = base_path("jiny/{$module}/App/Http/Controllers/Admin/Admin{$feature}");

        // Create directory if not exists
        if (! File::exists($controllerPath)) {
            File::makeDirectory($controllerPath, 0755, true);
        }

        // Controller file mappings
        $controllers = [
            'Admin.stub' => "Admin{$feature}.php",
            'AdminCreate.stub' => "Admin{$feature}Create.php",
            'AdminEdit.stub' => "Admin{$feature}Edit.php",
            'AdminDelete.stub' => "Admin{$feature}Delete.php",
            'AdminShow.stub' => "Admin{$feature}Show.php",
            'Admin.json.stub' => "Admin{$feature}.json",
        ];

        foreach ($controllers as $stub => $filename) {
            $stubPath = __DIR__."/../../../stubs/controller/{$stub}";
            $targetPath = "{$controllerPath}/{$filename}";

            if (File::exists($stubPath)) {
                $content = File::get($stubPath);

                // Replace placeholders
                $content = $this->replacePlaceholders($content, $module, $feature);

                File::put($targetPath, $content);
                $this->line("  - Created: {$filename}");
            }
        }
    }

    /**
     * 관리자 라우트를 admin.php 파일에 등록
     * 
     * 생성되는 라우트:
     * - GET /admin/{feature} : 리스트 페이지
     * - GET /admin/{feature}/create : 생성 폼 페이지
     * - GET /admin/{feature}/{id}/edit : 수정 폼 페이지
     * - GET /admin/{feature}/{id} : 상세 보기 페이지
     * - DELETE /admin/{feature}/{id} : 삭제 처리
     * 
     * 모든 라우트는 'web' 미들웨어 그룹과 '/admin' 프리픽스가 적용됩니다.
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $feature 기능 이름 (StudlyCase)
     * @param string $featureSnake 기능 이름 (snake_case, URL에 사용)
     * @return void
     */
    protected function registerRoutes($module, $feature, $featureSnake)
    {
        $this->info('Registering routes...');

        $routePath = base_path("jiny/{$module}/routes/admin.php");

        // Create routes directory and file if not exists
        if (! File::exists(dirname($routePath))) {
            File::makeDirectory(dirname($routePath), 0755, true);
        }

        if (! File::exists($routePath)) {
            $initialContent = "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n";
            File::put($routePath, $initialContent);
        }

        // Route template
        $routeTemplate = "
// Admin {$feature} Routes
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::group(['prefix' => '{$featureSnake}'], function () {
        Route::get('/', \\Jiny\\{$module}\\App\\Http\\Controllers\\Admin\\Admin{$feature}\\Admin{$feature}::class)
            ->name('admin.{$featureSnake}');
        
        Route::get('/create', \\Jiny\\{$module}\\App\\Http\\Controllers\\Admin\\Admin{$feature}\\Admin{$feature}Create::class)
            ->name('admin.{$featureSnake}.create');
        
        Route::get('/{id}/edit', \\Jiny\\{$module}\\App\\Http\\Controllers\\Admin\\Admin{$feature}\\Admin{$feature}Edit::class)
            ->name('admin.{$featureSnake}.edit');
        
        Route::get('/{id}', \\Jiny\\{$module}\\App\\Http\\Controllers\\Admin\\Admin{$feature}\\Admin{$feature}Show::class)
            ->name('admin.{$featureSnake}.show');
        
        Route::delete('/{id}', \\Jiny\\{$module}\\App\\Http\\Controllers\\Admin\\Admin{$feature}\\Admin{$feature}Delete::class)
            ->name('admin.{$featureSnake}.delete');
    });
});
";

        // Append routes to file
        File::append($routePath, $routeTemplate);
        $this->line('  - Routes registered in admin.php');
    }

    /**
     * 데이터베이스 마이그레이션 파일 생성
     * 
     * 생성되는 테이블 구조:
     * - id (bigIncrements): 기본 키
     * - title (string): 제목 필드
     * - description (text, nullable): 설명 필드
     * - enable (boolean): 활성화 여부
     * - pos (integer): 정렬 순서
     * - timestamps: created_at, updated_at
     * 
     * 테이블 이름은 'admin_{feature_plural}' 형식으로 생성됩니다.
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $tableName 테이블 이름 (복수형, snake_case)
     * @return void
     */
    protected function createMigration($module, $tableName)
    {
        $this->info('Creating migration...');

        $migrationPath = base_path("jiny/{$module}/database/migrations");

        // Create directory if not exists
        if (! File::exists($migrationPath)) {
            File::makeDirectory($migrationPath, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_admin_{$tableName}_table.php";
        $targetPath = "{$migrationPath}/{$filename}";

        $stubPath = __DIR__.'/../../../stubs/migration.stub';

        if (File::exists($stubPath)) {
            $content = File::get($stubPath);

            // Replace placeholders
            $content = str_replace('{{table}}', "admin_{$tableName}", $content);

            File::put($targetPath, $content);
            $this->line("  - Created migration: {$filename}");
        }
    }

    /**
     * Eloquent 모델 파일 생성
     * 
     * 생성되는 모델 특징:
     * - namespace: Jiny\{Module}\App\Models
     * - 클래스명: Admin{Feature}
     * - 테이블명: admin_{feature_plural}
     * - fillable 속성: title, description, enable, pos
     * - timestamps 자동 관리
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $feature 기능 이름 (StudlyCase)
     * @param string $tableName 테이블 이름 (복수형, snake_case)
     * @return void
     */
    protected function createModel($module, $feature, $tableName)
    {
        $this->info('Creating model...');

        $modelPath = base_path("jiny/{$module}/App/Models");

        // Create directory if not exists
        if (! File::exists($modelPath)) {
            File::makeDirectory($modelPath, 0755, true);
        }

        $filename = "Admin{$feature}.php";
        $targetPath = "{$modelPath}/{$filename}";

        $stubPath = __DIR__.'/../../../stubs/model.stub';

        if (File::exists($stubPath)) {
            $content = File::get($stubPath);

            // Replace placeholders
            $content = str_replace('{{Module}}', $module, $content);
            $content = str_replace('{{module}}', Str::snake($module), $content);
            $content = str_replace('{{Feature}}', $feature, $content);
            $content = str_replace('{{feature}}', Str::snake($feature), $content);
            $content = str_replace('{{table}}', "admin_{$tableName}", $content);

            File::put($targetPath, $content);
            $this->line("  - Created model: {$filename}");
        }
    }

    /**
     * Blade 뷰 템플릿 리소스 복사
     * 
     * 복사되는 뷰 파일:
     * - create.blade.php : 생성 폼 뷰
     * - edit.blade.php : 수정 폼 뷰
     * - show.blade.php : 상세 보기 뷰
     * - search.blade.php : 검색 폼 뷰
     * - table.blade.php : 데이터 테이블 뷰
     * 
     * 뷰 파일 경로: jiny/{module}/resources/views/admin/admin_{feature}/
     * 
     * 각 뷰 파일은 Livewire 컴포넌트와 연동되도록 설계되어 있으며,
     * 동적 데이터 바인딩과 실시간 업데이트를 지원합니다.
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $featureSnake 기능 이름 (snake_case)
     * @return void
     */
    protected function copyViewResources($module, $featureSnake)
    {
        $this->info('Copying view resources...');

        $viewPath = base_path("jiny/{$module}/resources/views/admin/admin_{$featureSnake}");

        // Create directory if not exists
        if (! File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
        }

        // View file mappings
        $views = [
            'create.blade.stub' => 'create.blade.php',
            'edit.blade.stub' => 'edit.blade.php',
            'show.blade.stub' => 'show.blade.php',
            'search.blade.stub' => 'search.blade.php',
            'table.blade.stub' => 'table.blade.php',
        ];

        foreach ($views as $stub => $filename) {
            $stubPath = __DIR__."/../../../stubs/views/{$stub}";
            $targetPath = "{$viewPath}/{$filename}";

            if (File::exists($stubPath)) {
                $content = File::get($stubPath);

                // Replace placeholders if needed
                $content = str_replace('{{Module}}', $module, $content);
                $content = str_replace('{{module}}', Str::snake($module), $content);
                $content = str_replace('{{feature}}', $featureSnake, $content);
                $content = str_replace('{{features}}', Str::plural($featureSnake), $content);
                $content = str_replace('{{Feature}}', Str::studly($featureSnake), $content);

                File::put($targetPath, $content);
                $this->line("  - Created view: {$filename}");
            }
        }
    }

    /**
     * 모델 팩토리 파일 생성
     * 
     * 테스트 및 개발 환경에서 사용할 수 있는 모델 팩토리를 생성합니다.
     * Faker 라이브러리를 사용하여 현실적인 테스트 데이터를 생성합니다.
     * 
     * 생성되는 가짜 데이터:
     * - title: Faker의 문장
     * - description: Faker의 단락
     * - enable: 랜덤 boolean
     * - pos: 1-100 사이의 랜덤 숫자
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $feature 기능 이름 (StudlyCase)
     * @return void
     */
    protected function createFactory($module, $feature)
    {
        $this->info('Creating factory...');

        $factoryPath = base_path('database/factories');

        // Create directory if not exists
        if (! File::exists($factoryPath)) {
            File::makeDirectory($factoryPath, 0755, true);
        }

        $filename = "Admin{$feature}Factory.php";
        $targetPath = "{$factoryPath}/{$filename}";

        $stubPath = __DIR__.'/../../../stubs/factory.stub';

        if (File::exists($stubPath)) {
            $content = File::get($stubPath);

            // Replace placeholders
            $content = $this->replacePlaceholders($content, $module, $feature);

            File::put($targetPath, $content);
            $this->line("  - Created factory: {$filename}");
        }
    }

    /**
     * 데이터베이스 시더 파일 생성
     * 
     * 개발 환경에서 사용할 수 있는 샘플 데이터를 생성합니다.
     * --with-seeder 옵션이 지정된 경우에만 생성됩니다.
     * 
     * 생성되는 샘플 데이터:
     * - 3개의 기본 레코드
     * - 2개는 활성화(enable=true), 1개는 비활성화(enable=false)
     * - 각각 다른 정렬 순서(pos) 값
     * 
     * 시더가 생성된 후 자동으로 실행되어 데이터베이스에
     * 샘플 데이터를 삽입합니다.
     * 
     * @param string $module 모듈 이름 (StudlyCase)
     * @param string $feature 기능 이름 (StudlyCase)
     * @param string $tableName 테이블 이름 (복수형, snake_case)
     * @return void
     */
    protected function createSeeder($module, $feature, $tableName)
    {
        $this->info('Creating seeder...');

        $seederPath = base_path('database/seeders');
        $filename = "Admin{$feature}Seeder.php";
        $targetPath = "{$seederPath}/{$filename}";

        $stubPath = __DIR__.'/../../../stubs/seeder.stub';

        if (File::exists($stubPath)) {
            $content = File::get($stubPath);

            // Replace placeholders
            $content = $this->replacePlaceholders($content, $module, $feature);

            File::put($targetPath, $content);
            $this->line("  - Created seeder: {$filename}");

            // Run the seeder
            $this->call('db:seed', ['--class' => "Admin{$feature}Seeder"]);
        } else {
            // Fallback to generated content if stub doesn't exist
            $seederContent = $this->generateSeederContent($feature, $tableName);
            File::put($targetPath, $seederContent);
            $this->line("  - Created seeder: {$filename} (generated)");
            $this->call('db:seed', ['--class' => "Admin{$feature}Seeder"]);
        }
    }

    /**
     * 시더 파일 내용 생성 (폴백 메서드)
     * 
     * 스텁 파일이 존재하지 않는 경우 사용되는 폴백 메서드입니다.
     * 프로그래밍 방식으로 시더 클래스 내용을 생성합니다.
     * 
     * 생성되는 클래스 구조:
     * - namespace: Database\Seeders
     * - 클래스명: Admin{Feature}Seeder
     * - run() 메서드에서 3개의 샘플 레코드 삽입
     * 
     * @param string $feature 기능 이름 (StudlyCase)
     * @param string $tableName 테이블 이름 (복수형, snake_case)
     * @return string PHP 시더 클래스 코드
     */
    protected function generateSeederContent($feature, $tableName)
    {
        return <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;
use Carbon\\Carbon;

class Admin{$feature}Seeder extends Seeder
{
    public function run(): void
    {
        \$now = Carbon::now();
        
        \$data = [
            [
                'title' => 'Sample {$feature} 1',
                'description' => 'This is a sample {$feature} entry for testing.',
                'enable' => true,
                'pos' => 1,
                'created_at' => \$now,
                'updated_at' => \$now,
            ],
            [
                'title' => 'Sample {$feature} 2',
                'description' => 'Another sample {$feature} entry.',
                'enable' => true,
                'pos' => 2,
                'created_at' => \$now,
                'updated_at' => \$now,
            ],
            [
                'title' => 'Disabled {$feature}',
                'description' => 'This {$feature} is disabled for testing.',
                'enable' => false,
                'pos' => 3,
                'created_at' => \$now,
                'updated_at' => \$now,
            ],
        ];
        
        DB::table('admin_{$tableName}')->insert(\$data);
    }
}
PHP;
    }

    /**
     * 스텁 템플릿의 플레이스홀더를 실제 값으로 치환
     * 
     * 지원되는 플레이스홀더:
     * - {{Module}} : 모듈명 (StudlyCase) 예: Shop
     * - {{module}} : 모듈명 (snake_case) 예: shop
     * - {{Feature}} : 기능명 (StudlyCase) 예: Product
     * - {{feature}} : 기능명 (snake_case) 예: product
     * - {{features}} : 기능명 복수형 (snake_case) 예: products
     * - {{table}} : 테이블명 예: admin_products
     * 
     * 이 메서드는 모든 스텁 파일에서 사용되어 템플릿을
     * 실제 사용 가능한 코드로 변환합니다.
     * 
     * @param string $content 원본 스텁 내용
     * @param string $module 모듈 이름
     * @param string $feature 기능 이름
     * @return string 플레이스홀더가 치환된 내용
     */
    protected function replacePlaceholders($content, $module, $feature)
    {
        $replacements = [
            '{{Module}}' => Str::studly($module),
            '{{module}}' => Str::snake($module),
            '{{Feature}}' => Str::studly($feature),
            '{{feature}}' => Str::snake($feature),
            '{{features}}' => Str::plural(Str::snake($feature)),
            '{{table}}' => 'admin_'.Str::plural(Str::snake($feature)),
        ];

        foreach ($replacements as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    /**
     * 데이터베이스 마이그레이션 실행
     * 
     * 생성된 마이그레이션 파일을 실행하여 데이터베이스에
     * 실제 테이블을 생성합니다.
     * 
     * --no-migration 옵션이 지정된 경우 이 메서드는 호출되지 않습니다.
     * 
     * 내부적으로 'php artisan migrate' 명령을 실행하며,
     * 모든 대기 중인 마이그레이션이 함께 실행됩니다.
     * 
     * @return void
     */
    protected function runMigration()
    {
        $this->info('Running migration...');

        $this->call('migrate');
    }
}
