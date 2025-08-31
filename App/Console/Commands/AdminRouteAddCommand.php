<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminRouteAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:route-add {module : The module name} {feature : The feature name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add or restore routes for an Admin CRUD controller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $feature = $this->argument('feature');
        
        // Convert to proper case
        $moduleStudly = Str::studly($module);
        $featureStudly = Str::studly($feature);
        $featureSnake = Str::snake($feature);
        
        $this->info("Checking routes for {$moduleStudly}::{$featureStudly}...");
        
        // Check if controller files exist
        if (!$this->checkControllerExists($moduleStudly, $featureStudly)) {
            $this->error("Controller files not found for {$moduleStudly}::{$featureStudly}");
            $this->line("Please run 'php artisan admin:make {$module} {$feature}' first to create the controllers.");
            return 1;
        }
        
        // Check if routes already exist
        if ($this->checkRoutesExist($moduleStudly, $featureSnake)) {
            $this->info("Routes already exist for {$moduleStudly}::{$featureStudly}");
            $this->displayExistingRoutes($featureSnake);
            return 0;
        }
        
        // Add routes
        $this->addRoutes($moduleStudly, $featureStudly, $featureSnake);
        
        $this->info("Routes added successfully for {$moduleStudly}::{$featureStudly}!");
        $this->displayRouteInfo($featureSnake);
        
        return 0;
    }
    
    /**
     * Check if controller files exist
     */
    protected function checkControllerExists($module, $feature)
    {
        $controllerPath = base_path("jiny/{$module}/App/Http/Controllers/Admin/Admin{$feature}");
        
        if (!File::exists($controllerPath)) {
            return false;
        }
        
        // Check for main controller file
        $mainController = "{$controllerPath}/Admin{$feature}.php";
        if (!File::exists($mainController)) {
            return false;
        }
        
        $this->line("✓ Controllers found at: " . str_replace(base_path(), '', $controllerPath));
        return true;
    }
    
    /**
     * Check if routes already exist
     */
    protected function checkRoutesExist($module, $featureSnake)
    {
        $routePath = base_path("jiny/{$module}/routes/admin.php");
        
        if (!File::exists($routePath)) {
            return false;
        }
        
        $content = File::get($routePath);
        
        // Check if route with this prefix already exists
        if (preg_match("/Route::group\(\['prefix' => '{$featureSnake}'\]/", $content)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Add routes to admin.php
     */
    protected function addRoutes($module, $feature, $featureSnake)
    {
        $this->info("Adding routes...");
        
        $routePath = base_path("jiny/{$module}/routes/admin.php");
        
        // Create routes directory and file if not exists
        if (!File::exists(dirname($routePath))) {
            File::makeDirectory(dirname($routePath), 0755, true);
            $this->line("  - Created routes directory");
        }
        
        if (!File::exists($routePath)) {
            $initialContent = "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n";
            File::put($routePath, $initialContent);
            $this->line("  - Created admin.php route file");
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
        $this->line("  - Routes added to admin.php");
    }
    
    /**
     * Display existing routes information
     */
    protected function displayExistingRoutes($featureSnake)
    {
        $this->newLine();
        $this->table(
            ['Route Name', 'Method', 'URI'],
            [
                ["admin.{$featureSnake}", 'GET', "/admin/{$featureSnake}"],
                ["admin.{$featureSnake}.create", 'GET', "/admin/{$featureSnake}/create"],
                ["admin.{$featureSnake}.edit", 'GET', "/admin/{$featureSnake}/{id}/edit"],
                ["admin.{$featureSnake}.show", 'GET', "/admin/{$featureSnake}/{id}"],
                ["admin.{$featureSnake}.delete", 'DELETE', "/admin/{$featureSnake}/{id}"],
            ]
        );
    }
    
    /**
     * Display route information
     */
    protected function displayRouteInfo($featureSnake)
    {
        $this->newLine();
        $this->comment("Available routes:");
        $this->table(
            ['Route Name', 'Method', 'URI'],
            [
                ["admin.{$featureSnake}", 'GET', "/admin/{$featureSnake}"],
                ["admin.{$featureSnake}.create", 'GET', "/admin/{$featureSnake}/create"],
                ["admin.{$featureSnake}.edit", 'GET', "/admin/{$featureSnake}/{id}/edit"],
                ["admin.{$featureSnake}.show", 'GET', "/admin/{$featureSnake}/{id}"],
                ["admin.{$featureSnake}.delete", 'DELETE', "/admin/{$featureSnake}/{id}"],
            ]
        );
        
        $this->newLine();
        $this->line("You can now access your admin panel at:");
        $this->info("  " . url("/admin/{$featureSnake}"));
    }
}