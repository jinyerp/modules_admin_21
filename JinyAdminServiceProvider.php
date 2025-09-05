<?php

namespace Jiny\Admin;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

class JinyAdminServiceProvider extends ServiceProvider
{
    private $package = 'jiny-admin';

    public function boot()
    {
        // 미들웨어 등록
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('admin', \Jiny\Admin\App\Http\Middleware\AdminMiddleware::class);

        // 모듈: 라우트 설정
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/admin.php');

        $this->loadViewsFrom(__DIR__.'/resources/views', $this->package);

        // 설정파일 복사
        // php artisan vendor:publish --tag=admin-config
        $this->publishes([
            __DIR__.'/config/setting.php' => config_path('admin/setting.php'),
        ], 'admin-config');

        // 데이터베이스
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Artisan 명령어 등록
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Jiny\Admin\App\Console\Commands\AdminMakeCommand::class,
                \Jiny\Admin\App\Console\Commands\AdminRemoveCommand::class,
                \Jiny\Admin\App\Console\Commands\AdminRouteAddCommand::class,
                \Jiny\Admin\App\Console\Commands\UnblockPasswordAttempts::class,
                \Jiny\Admin\App\Console\Commands\ResetPasswordAttempts::class,
            ]);
        }
    }

    public function register()
    {
        // 설정 파일 병합
        $this->mergeConfigFrom(
            __DIR__.'/config/setting.php', 'setting'
        );

        // Livewire 컴포넌트 등록 (Jetstream 방식)
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                // Generic Admin Components
                Livewire::component('jiny-admin::admin-table', \Jiny\Admin\App\Http\Livewire\AdminTable::class);
                Livewire::component('jiny-admin::admin-create', \Jiny\Admin\App\Http\Livewire\AdminCreate::class);
                Livewire::component('jiny-admin::admin-edit', \Jiny\Admin\App\Http\Livewire\AdminEdit::class);
                Livewire::component('jiny-admin::admin-show', \Jiny\Admin\App\Http\Livewire\AdminShow::class);
                Livewire::component('jiny-admin::admin-search', \Jiny\Admin\App\Http\Livewire\AdminSearch::class);
                Livewire::component('jiny-admin::admin-delete', \Jiny\Admin\App\Http\Livewire\AdminDelete::class);

                Livewire::component('jiny-admin::admin-table-setting', \Jiny\Admin\App\Http\Livewire\AdminTableSetting::class);

                // Header and Settings Components
                Livewire::component('jiny-admin::admin-header-with-settings', \Jiny\Admin\App\Http\Livewire\AdminHeaderWithSettings::class);

                // Settings Components
                Livewire::component('jiny-admin::settings.table-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\TableSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.show-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\ShowSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.create-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\CreateSettingsDrawer::class);
                Livewire::component('jiny-admin::settings.edit-settings-drawer', \Jiny\Admin\App\Http\Livewire\Settings\EditSettingsDrawer::class);
            }
        });
    }
}
