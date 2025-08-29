<?php
namespace Jiny\Admin2;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Illuminate\Routing\Router;

class JinyAdmin2ServiceProvider extends ServiceProvider
{
    private $package = "jiny-admin2";

    public function boot()
    {
        // 모듈: 라우트 설정
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/admin.php');

        $this->loadViewsFrom(__DIR__.'/resources/views', $this->package);

        // 설정파일 복사
        // php artisan vendor:publish --tag=admin-config
        $this->publishes([
            __DIR__.'/config/setting.php' => config_path('admin/setting2.php'),
        ],'admin2-config');

        // 데이터베이스
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        // Livewire 컴포넌트 등록 (Jetstream 방식)
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                // Generic Admin Components
                Livewire::component('jiny-admin2::admin-table', \Jiny\Admin2\App\Http\Livewire\AdminTable::class);
                Livewire::component('jiny-admin2::admin-create', \Jiny\Admin2\App\Http\Livewire\AdminCreate::class);
                Livewire::component('jiny-admin2::admin-edit', \Jiny\Admin2\App\Http\Livewire\AdminEdit::class);
                Livewire::component('jiny-admin2::admin-show', \Jiny\Admin2\App\Http\Livewire\AdminShow::class);
                Livewire::component('jiny-admin2::admin-search', \Jiny\Admin2\App\Http\Livewire\AdminSearch::class);

                Livewire::component('jiny-admin2::admin-table-setting', \Jiny\Admin2\App\Http\Livewire\AdminTableSetting::class);
            }
        });
    }
}
