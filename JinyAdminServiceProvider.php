<?php

namespace Jiny\Admin;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Jiny\Admin\App\Services\Captcha\CaptchaManager;

/**
 * Jiny Admin Service Provider
 * 
 * Laravel 애플리케이션에 Jiny Admin 패키지를 등록하고 설정하는 서비스 프로바이더
 * 
 * @package Jiny\Admin
 * @author JinyPHP Team
 * @version 1.0.0
 */
class JinyAdminServiceProvider extends ServiceProvider
{
    /**
     * 패키지 식별자
     * 
     * @var string
     */
    private $package = 'jiny-admin';

    /**
     * 패키지 부팅 메서드
     * 
     * 라우트, 뷰, 마이그레이션, 명령어 등을 등록합니다.
     * 
     * @return void
     */
    public function boot()
    {
        // ========================================
        // 1. 미들웨어 등록
        // ========================================
        $this->registerMiddleware();

        // ========================================
        // 2. 라우트 파일 로드
        // ========================================
        $this->loadRoutes();

        // ========================================
        // 3. 뷰 리소스 등록
        // ========================================
        $this->loadViewsFrom(__DIR__.'/resources/views', $this->package);

        // ========================================
        // 4. 설정 파일 퍼블리싱
        // ========================================
        $this->publishConfiguration();

        // ========================================
        // 5. 데이터베이스 마이그레이션
        // ========================================
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // ========================================
        // 6. Artisan 명령어 등록 (콘솔 환경에서만)
        // ========================================
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * 패키지 등록 메서드
     * 
     * 설정 파일 병합 및 서비스 컨테이너 바인딩을 처리합니다.
     * 
     * @return void
     */
    public function register()
    {
        // ========================================
        // 1. 설정 파일 병합
        // ========================================
        $this->mergeConfiguration();

        // ========================================
        // 2. 서비스 컨테이너 바인딩
        // ========================================
        $this->registerServices();

        // ========================================
        // 3. Livewire 컴포넌트 등록
        // ========================================
        $this->registerLivewireComponents();
    }

    /**
     * 미들웨어 등록
     * 
     * @return void
     */
    protected function registerMiddleware()
    {
        $router = $this->app->make(Router::class);
        
        // 관리자 접근 제어 미들웨어
        $router->aliasMiddleware('admin', \Jiny\Admin\App\Http\Middleware\AdminMiddleware::class);
        
        // IP 화이트리스트 미들웨어
        $router->aliasMiddleware('ip.whitelist', \Jiny\Admin\App\Http\Middleware\IpWhitelistMiddleware::class);
        
        // CAPTCHA 검증 미들웨어
        $router->aliasMiddleware('captcha', \Jiny\Admin\App\Http\Middleware\CaptchaMiddleware::class);
        
        // 비밀번호 변경 체크 미들웨어
        $router->aliasMiddleware('check.password.change', \Jiny\Admin\App\Http\Middleware\CheckPasswordChange::class);
    }

    /**
     * 라우트 파일 로드
     * 
     * @return void
     */
    protected function loadRoutes()
    {
        // 웹 라우트 (로그인, 로그아웃 등)
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        
        // API 라우트
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        
        // 관리자 라우트 (관리자 페이지, 2FA 포함)
        $this->loadRoutesFrom(__DIR__.'/routes/admin.php');
    }

    /**
     * 설정 파일 퍼블리싱
     * 
     * php artisan vendor:publish --tag=jiny-admin-config
     * php artisan vendor:publish --tag=jiny-admin-assets
     * 
     * @return void
     */
    protected function publishConfiguration()
    {
        // 설정 파일 퍼블리싱
        $this->publishes([
            __DIR__.'/config/setting.php' => config_path('admin/setting.php'),
            __DIR__.'/config/captcha.php' => config_path('captcha.php'),
            __DIR__.'/config/mail.php' => config_path('admin/mail.php'),
        ], 'jiny-admin-config');

        // 에셋 파일 퍼블리싱 (필요한 경우)
        $this->publishes([
            __DIR__.'/resources/assets' => public_path('vendor/jiny-admin'),
        ], 'jiny-admin-assets');
    }

    /**
     * Artisan 명령어 등록
     * 
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            // ========================================
            // Admin 모듈 생성/관리 명령어
            // ========================================
            \Jiny\Admin\App\Console\Commands\AdminMakeCommand::class,          // admin:make
            \Jiny\Admin\App\Console\Commands\AdminRemoveCommand::class,        // admin:remove
            \Jiny\Admin\App\Console\Commands\AdminRouteAddCommand::class,      // admin:route-add
            \Jiny\Admin\App\Console\Commands\AdminMakeJsonCommand::class,      // admin:make-json
            \Jiny\Admin\App\Console\Commands\AdminMakeControllerCommand::class,// admin:make-controller
            \Jiny\Admin\App\Console\Commands\AdminMakeViewCommand::class,      // admin:make-view

            // ========================================
            // 관리자 계정 관리 명령어
            // ========================================
            \Jiny\Admin\App\Console\Commands\AdminCreate::class,               // admin:create
            \Jiny\Admin\App\Console\Commands\AdminDelete::class,               // admin:delete
            \Jiny\Admin\App\Console\Commands\AdminList::class,                 // admin:list
            \Jiny\Admin\App\Console\Commands\AdminPasswordReset::class,        // admin:password-reset

            // ========================================
            // 보안 관련 명령어
            // ========================================
            \Jiny\Admin\App\Console\Commands\UnblockPasswordAttempts::class,   // admin:unblock-password
            \Jiny\Admin\App\Console\Commands\ResetPasswordAttempts::class,     // admin:reset-attempts
            \Jiny\Admin\App\Console\Commands\CaptchaLogs::class,              // admin:captcha-logs
            
            // ========================================
            // IP 관리 명령어
            // ========================================
            \Jiny\Admin\App\Console\Commands\IpCleanup::class,                // admin:ip-cleanup
            \Jiny\Admin\App\Console\Commands\IpUnblock::class,                // admin:ip-unblock
            \Jiny\Admin\App\Console\Commands\IpStats::class,                  // admin:ip-stats
        ]);
    }

    /**
     * 설정 파일 병합
     * 
     * @return void
     */
    protected function mergeConfiguration()
    {
        // 기본 설정 파일 병합
        $this->mergeConfigFrom(
            __DIR__.'/config/setting.php', 'admin.setting'
        );

        // 메일 설정 파일 병합
        $this->mergeConfigFrom(
            __DIR__.'/config/mail.php', 'admin.mail'
        );

        // CAPTCHA 설정 파일 병합
        $this->mergeConfigFrom(
            __DIR__.'/config/captcha.php', 'captcha'
        );
    }

    /**
     * 서비스 컨테이너 바인딩 등록
     * 
     * @return void
     */
    protected function registerServices()
    {
        // CAPTCHA Manager 싱글톤 등록
        $this->app->singleton(CaptchaManager::class, function ($app) {
            return new CaptchaManager($app);
        });

        // 추가 서비스 바인딩이 필요한 경우 여기에 등록
        // $this->app->singleton(SmsService::class, function ($app) {
        //     return new SmsService($app);
        // });
    }

    /**
     * Livewire 컴포넌트 등록
     * 
     * @return void
     */
    protected function registerLivewireComponents()
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                // ========================================
                // 기본 Admin CRUD 컴포넌트
                // ========================================
                Livewire::component('jiny-admin::admin-table', \Jiny\Admin\App\Http\Livewire\AdminTable::class);
                Livewire::component('jiny-admin::admin-create', \Jiny\Admin\App\Http\Livewire\AdminCreate::class);
                Livewire::component('jiny-admin::admin-edit', \Jiny\Admin\App\Http\Livewire\AdminEdit::class);
                Livewire::component('jiny-admin::admin-show', \Jiny\Admin\App\Http\Livewire\AdminShow::class);
                Livewire::component('jiny-admin::admin-search', \Jiny\Admin\App\Http\Livewire\AdminSearch::class);
                Livewire::component('jiny-admin::admin-delete', \Jiny\Admin\App\Http\Livewire\AdminDelete::class);

                // ========================================
                // UI 컴포넌트
                // ========================================
                Livewire::component('jiny-admin::admin-notification', \Jiny\Admin\App\Http\Livewire\AdminNotification::class);
                Livewire::component('jiny-admin::admin-table-setting', \Jiny\Admin\App\Http\Livewire\AdminTableSetting::class);
                Livewire::component('jiny-admin::admin-header-with-settings', \Jiny\Admin\App\Http\Livewire\AdminHeaderWithSettings::class);

                // ========================================
                // 설정 드로어 컴포넌트
                // ========================================
                Livewire::component('jiny-admin::settings.table-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\TableSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.show-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\ShowSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.create-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\CreateSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.edit-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\EditSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.detail-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\DetailSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.settings-button', \Jiny\Admin\App\Http\Livewire\Settings\SettingsButton::class);

                // ========================================
                // 특수 기능 컴포넌트
                // ========================================
                Livewire::component('jiny-admin::admin-captcha-logs', \Jiny\Admin\App\Http\Livewire\AdminCaptchaLogs::class);
            }
        });
    }

    /**
     * 패키지에서 제공하는 서비스 목록
     * 
     * @return array
     */
    public function provides()
    {
        return [
            CaptchaManager::class,
        ];
    }
}