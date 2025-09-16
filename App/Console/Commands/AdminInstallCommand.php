<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdminInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install 
                            {--force : 강제로 덮어쓰기}
                            {--skip-tailwind : Tailwind CSS 설정 건너뛰기}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Jiny Admin 패키지 설치 및 설정';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Jiny Admin 패키지 설치를 시작합니다...');
        
        // 1. Tailwind CSS 설정
        if (!$this->option('skip-tailwind')) {
            $this->configureTailwindCSS();
        }
        
        // 2. 설정 파일 발행
        $this->publishConfig();
        
        // 3. 마이그레이션 실행 여부 확인
        if ($this->confirm('데이터베이스 마이그레이션을 실행하시겠습니까?')) {
            $this->call('migrate');
        }
        
        // 4. 관리자 계정 생성
        if ($this->confirm('관리자 계정을 생성하시겠습니까?')) {
            $this->call('admin:user-create');
        }
        
        $this->info('✅ Jiny Admin 패키지 설치가 완료되었습니다!');
        $this->info('');
        $this->info('다음 단계:');
        $this->info('1. npm run build (또는 npm run dev) 실행하여 CSS 빌드');
        $this->info('2. php artisan serve 로 서버 시작');
        $this->info('3. http://localhost:8000/admin 접속');
        
        return Command::SUCCESS;
    }
    
    /**
     * Tailwind CSS 설정 업데이트
     */
    protected function configureTailwindCSS()
    {
        $this->info('📦 Tailwind CSS 설정을 업데이트합니다...');
        
        $appCssPath = resource_path('css/app.css');
        
        // Tailwind v4 사용 여부 확인
        if (File::exists($appCssPath)) {
            $content = File::get($appCssPath);
            
            // Tailwind v4 (@source 사용)
            if (str_contains($content, '@source')) {
                $this->configureTailwindV4($appCssPath, $content);
            }
            // Tailwind v3 (tailwind.config.js 사용)
            elseif (File::exists(base_path('tailwind.config.js'))) {
                $this->configureTailwindV3();
            }
            else {
                $this->warn('⚠️ Tailwind CSS 설정을 감지할 수 없습니다.');
                $this->info('수동으로 다음 경로를 Tailwind 설정에 추가해주세요:');
                $this->info('- vendor/jinyerp/**/*.blade.php');
                $this->info('- vendor/jiny/**/*.blade.php');
            }
        }
    }
    
    /**
     * Tailwind CSS v4 설정 (app.css에 @source 추가)
     */
    protected function configureTailwindV4($path, $content)
    {
        $sources = [
            "@source '../../vendor/jinyerp/**/*.blade.php';",
            "@source '../../vendor/jinyerp/**/*.php';",
            "@source '../../vendor/jiny/**/*.blade.php';",
            "@source '../../vendor/jiny/**/*.php';",
        ];
        
        $needsUpdate = false;
        $linesToAdd = [];
        
        foreach ($sources as $source) {
            if (!str_contains($content, $source)) {
                $needsUpdate = true;
                $linesToAdd[] = $source;
            }
        }
        
        if ($needsUpdate) {
            // 백업 생성
            File::copy($path, $path . '.backup');
            $this->info('✅ app.css 백업 생성: ' . $path . '.backup');
            
            // @theme 앞에 source 추가
            if (str_contains($content, '@theme')) {
                $content = str_replace('@theme', implode("\n", $linesToAdd) . "\n\n@theme", $content);
            }
            // 파일 끝에 추가
            else {
                $content .= "\n" . implode("\n", $linesToAdd) . "\n";
            }
            
            File::put($path, $content);
            $this->info('✅ Tailwind CSS v4 설정이 업데이트되었습니다.');
        } else {
            $this->info('✅ Tailwind CSS v4 설정이 이미 최신 상태입니다.');
        }
    }
    
    /**
     * Tailwind CSS v3 설정 (tailwind.config.js 수정)
     */
    protected function configureTailwindV3()
    {
        $configPath = base_path('tailwind.config.js');
        $config = File::get($configPath);
        
        $paths = [
            "'./vendor/jinyerp/**/*.blade.php'",
            "'./vendor/jiny/**/*.blade.php'",
        ];
        
        $needsUpdate = false;
        foreach ($paths as $path) {
            if (!str_contains($config, $path)) {
                $needsUpdate = true;
            }
        }
        
        if ($needsUpdate) {
            // 백업 생성
            File::copy($configPath, $configPath . '.backup');
            $this->info('✅ tailwind.config.js 백업 생성');
            
            // content 배열에 경로 추가
            $config = preg_replace(
                '/content:\s*\[([^\]]*)\]/s',
                "content: [$1,\n        './vendor/jinyerp/**/*.blade.php',\n        './vendor/jiny/**/*.blade.php'\n    ]",
                $config
            );
            
            File::put($configPath, $config);
            $this->info('✅ Tailwind CSS v3 설정이 업데이트되었습니다.');
        } else {
            $this->info('✅ Tailwind CSS v3 설정이 이미 최신 상태입니다.');
        }
    }
    
    /**
     * 설정 파일 발행
     */
    protected function publishConfig()
    {
        $this->info('📋 설정 파일을 발행합니다...');
        
        if (!$this->option('force') && File::exists(config_path('admin/setting.php'))) {
            if (!$this->confirm('설정 파일이 이미 존재합니다. 덮어쓰시겠습니까?')) {
                return;
            }
        }
        
        $this->call('vendor:publish', [
            '--tag' => 'jiny-admin-config',
            '--force' => $this->option('force'),
        ]);
    }
}