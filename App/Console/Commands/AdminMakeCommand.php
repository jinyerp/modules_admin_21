<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make {module : The module name} {feature : The feature name} 
                            {--with-seeder : Create a seeder with sample data}
                            {--fields= : Comma-separated list of additional fields (e.g., name:string,price:decimal)}
                            {--no-migration : Skip migration creation and execution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Admin CRUD controller with all necessary files';

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
        $featurePlural = Str::plural($featureSnake);
        
        $this->info("Creating Admin CRUD for {$moduleStudly}::{$featureStudly}");
        
        // Step 1: Create Controllers
        $this->createControllers($moduleStudly, $featureStudly);
        
        // Step 2: Register Routes
        $this->registerRoutes($moduleStudly, $featureStudly, $featureSnake);
        
        // Step 3: Create Migration
        if (!$this->option('no-migration')) {
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
        if (!$this->option('no-migration')) {
            $this->runMigration();
        }
        
        $this->info("Admin CRUD for {$moduleStudly}::{$featureStudly} created successfully!");
        $this->info("Don't forget to register your module's service provider if not already done.");
    }
    
    /**
     * Create controller files from stubs
     */
    protected function createControllers($module, $feature)
    {
        $this->info("Creating controllers...");
        
        $controllerPath = base_path("jiny/{$module}/App/Http/Controllers/Admin/Admin{$feature}");
        
        // Create directory if not exists
        if (!File::exists($controllerPath)) {
            File::makeDirectory($controllerPath, 0755, true);
        }
        
        // Controller file mappings
        $controllers = [
            'Admin.stub' => "Admin{$feature}.php",
            'AdminCreate.stub' => "Admin{$feature}Create.php",
            'AdminEdit.stub' => "Admin{$feature}Edit.php",
            'AdminDelete.stub' => "Admin{$feature}Delete.php",
            'AdminShow.stub' => "Admin{$feature}Show.php",
            'Admin.json.stub' => "Admin{$feature}.json"
        ];
        
        foreach ($controllers as $stub => $filename) {
            $stubPath = __DIR__ . "/../../../stubs/controller/{$stub}";
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
     * Register routes in admin.php
     */
    protected function registerRoutes($module, $feature, $featureSnake)
    {
        $this->info("Registering routes...");
        
        $routePath = base_path("jiny/{$module}/routes/admin.php");
        
        // Create routes directory and file if not exists
        if (!File::exists(dirname($routePath))) {
            File::makeDirectory(dirname($routePath), 0755, true);
        }
        
        if (!File::exists($routePath)) {
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
        $this->line("  - Routes registered in admin.php");
    }
    
    /**
     * Create migration file
     */
    protected function createMigration($module, $tableName)
    {
        $this->info("Creating migration...");
        
        $migrationPath = base_path("jiny/{$module}/database/migrations");
        
        // Create directory if not exists
        if (!File::exists($migrationPath)) {
            File::makeDirectory($migrationPath, 0755, true);
        }
        
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_admin_{$tableName}_table.php";
        $targetPath = "{$migrationPath}/{$filename}";
        
        $stubPath = __DIR__ . "/../../../stubs/migration.stub";
        
        if (File::exists($stubPath)) {
            $content = File::get($stubPath);
            
            // Replace placeholders
            $content = str_replace('{{table}}', "admin_{$tableName}", $content);
            
            File::put($targetPath, $content);
            $this->line("  - Created migration: {$filename}");
        }
    }
    
    /**
     * Create model file
     */
    protected function createModel($module, $feature, $tableName)
    {
        $this->info("Creating model...");
        
        $modelPath = base_path("jiny/{$module}/App/Models");
        
        // Create directory if not exists
        if (!File::exists($modelPath)) {
            File::makeDirectory($modelPath, 0755, true);
        }
        
        $filename = "Admin{$feature}.php";
        $targetPath = "{$modelPath}/{$filename}";
        
        $stubPath = __DIR__ . "/../../../stubs/model.stub";
        
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
     * Copy view resources
     */
    protected function copyViewResources($module, $featureSnake)
    {
        $this->info("Copying view resources...");
        
        $viewPath = base_path("jiny/{$module}/resources/views/admin/admin_{$featureSnake}");
        
        // Create directory if not exists
        if (!File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
        }
        
        // View file mappings
        $views = [
            'create.blade.stub' => 'create.blade.php',
            'edit.blade.stub' => 'edit.blade.php',
            'show.blade.stub' => 'show.blade.php',
            'search.blade.stub' => 'search.blade.php',
            'table.blade.stub' => 'table.blade.php'
        ];
        
        foreach ($views as $stub => $filename) {
            $stubPath = __DIR__ . "/../../../stubs/views/{$stub}";
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
     * Create factory file
     */
    protected function createFactory($module, $feature)
    {
        $this->info("Creating factory...");
        
        $factoryPath = base_path("database/factories");
        
        // Create directory if not exists
        if (!File::exists($factoryPath)) {
            File::makeDirectory($factoryPath, 0755, true);
        }
        
        $filename = "Admin{$feature}Factory.php";
        $targetPath = "{$factoryPath}/{$filename}";
        
        $stubPath = __DIR__ . "/../../../stubs/factory.stub";
        
        if (File::exists($stubPath)) {
            $content = File::get($stubPath);
            
            // Replace placeholders
            $content = $this->replacePlaceholders($content, $module, $feature);
            
            File::put($targetPath, $content);
            $this->line("  - Created factory: {$filename}");
        }
    }
    
    /**
     * Create seeder file
     */
    protected function createSeeder($module, $feature, $tableName)
    {
        $this->info("Creating seeder...");
        
        $seederPath = base_path("database/seeders");
        $filename = "Admin{$feature}Seeder.php";
        $targetPath = "{$seederPath}/{$filename}";
        
        $stubPath = __DIR__ . "/../../../stubs/seeder.stub";
        
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
     * Generate seeder content
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
     * Replace placeholders in content
     */
    protected function replacePlaceholders($content, $module, $feature)
    {
        $replacements = [
            '{{Module}}' => Str::studly($module),
            '{{module}}' => Str::snake($module),
            '{{Feature}}' => Str::studly($feature),
            '{{feature}}' => Str::snake($feature),
            '{{features}}' => Str::plural(Str::snake($feature)),
            '{{table}}' => 'admin_' . Str::plural(Str::snake($feature)),
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
        }
        
        return $content;
    }
    
    /**
     * Run migration
     */
    protected function runMigration()
    {
        $this->info("Running migration...");
        
        $this->call('migrate');
    }
}