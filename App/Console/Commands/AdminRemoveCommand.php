<?php

namespace Jiny\Admin\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdminRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:remove {module : The module name} {feature : The feature name} {--force : Force removal without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an Admin CRUD controller with all related files';

    /**
     * Files to be removed
     *
     * @var array
     */
    protected $filesToRemove = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $feature = $this->argument('feature');
        $force = $this->option('force');
        
        // Convert to proper case
        $moduleStudly = Str::studly($module);
        $featureStudly = Str::studly($feature);
        $featureSnake = Str::snake($feature);
        $featurePlural = Str::plural($featureSnake);
        
        $this->warn("Preparing to remove Admin CRUD for {$moduleStudly}::{$featureStudly}");
        
        // Collect all files to be removed
        $this->collectFilesToRemove($moduleStudly, $featureStudly, $featureSnake, $featurePlural);
        
        // Display files to be removed
        $this->displayFilesToRemove();
        
        // Confirm removal
        if (!$force && !$this->confirm('Do you want to proceed with removing these files?')) {
            $this->info('Operation cancelled.');
            return;
        }
        
        // Remove files
        $this->removeFiles();
        
        // Remove routes
        $this->removeRoutes($moduleStudly, $featureSnake);
        
        // Drop database table if exists
        $this->dropTable($featurePlural);
        
        // Clean up empty directories
        $this->cleanupEmptyDirectories($moduleStudly, $featureSnake);
        
        $this->info("Admin CRUD for {$moduleStudly}::{$featureStudly} removed successfully!");
    }
    
    /**
     * Collect all files to be removed
     */
    protected function collectFilesToRemove($module, $feature, $featureSnake, $featurePlural)
    {
        // Controllers
        $controllerPath = base_path("jiny/{$module}/App/Http/Controllers/Admin/Admin{$feature}");
        if (File::exists($controllerPath)) {
            $this->filesToRemove['Controllers'] = [
                "{$controllerPath}/Admin{$feature}.php",
                "{$controllerPath}/Admin{$feature}Create.php",
                "{$controllerPath}/Admin{$feature}Edit.php",
                "{$controllerPath}/Admin{$feature}Delete.php",
                "{$controllerPath}/Admin{$feature}Show.php",
                "{$controllerPath}/Admin{$feature}.json"
            ];
        }
        
        // Model
        $modelPath = base_path("jiny/{$module}/App/Models/Admin{$feature}.php");
        if (File::exists($modelPath)) {
            $this->filesToRemove['Model'] = [$modelPath];
        }
        
        // Views
        $viewPath = base_path("jiny/{$module}/resources/views/admin/admin_{$featureSnake}");
        if (File::exists($viewPath)) {
            $this->filesToRemove['Views'] = [
                "{$viewPath}/create.blade.php",
                "{$viewPath}/edit.blade.php",
                "{$viewPath}/show.blade.php",
                "{$viewPath}/search.blade.php",
                "{$viewPath}/table.blade.php"
            ];
        }
        
        // Migration files
        $migrationPath = base_path("jiny/{$module}/database/migrations");
        if (File::exists($migrationPath)) {
            $migrationFiles = File::glob("{$migrationPath}/*_create_admin_{$featurePlural}_table.php");
            if (!empty($migrationFiles)) {
                $this->filesToRemove['Migrations'] = $migrationFiles;
            }
        }
    }
    
    /**
     * Display files to be removed
     */
    protected function displayFilesToRemove()
    {
        $this->info("The following files will be removed:");
        $this->newLine();
        
        foreach ($this->filesToRemove as $category => $files) {
            $this->comment("  {$category}:");
            foreach ($files as $file) {
                if (File::exists($file)) {
                    $this->line("    ✓ " . str_replace(base_path(), '', $file));
                } else {
                    $this->line("    ✗ " . str_replace(base_path(), '', $file) . " (not found)");
                }
            }
        }
        
        $this->newLine();
        $this->warn("Additional changes:");
        $this->line("  - Routes will be removed from admin.php");
        $this->line("  - Database table will be dropped if exists");
        $this->line("  - Empty directories will be cleaned up");
        $this->newLine();
    }
    
    /**
     * Remove files
     */
    protected function removeFiles()
    {
        $this->info("Removing files...");
        
        foreach ($this->filesToRemove as $category => $files) {
            foreach ($files as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                    $this->line("  - Removed: " . basename($file));
                }
            }
        }
    }
    
    /**
     * Remove routes from admin.php
     */
    protected function removeRoutes($module, $featureSnake)
    {
        $this->info("Removing routes...");
        
        $routePath = base_path("jiny/{$module}/routes/admin.php");
        
        if (!File::exists($routePath)) {
            $this->line("  - Route file not found, skipping...");
            return;
        }
        
        $content = File::get($routePath);
        $featureStudly = Str::studly($featureSnake);
        
        // More specific pattern that only matches the exact feature routes
        // Match the comment line and the entire route block for this specific feature
        $pattern = "/\n*\/\/ Admin " . preg_quote($featureStudly, '/') . " Routes\s*\n" .
                   "Route::middleware\(\['web'\]\)->prefix\('admin'\)->group\(function \(\) \{\s*\n" .
                   "\s*Route::group\(\['prefix' => '" . preg_quote($featureSnake, '/') . "'\], function \(\) \{[^}]*?\}\);\s*\n" .
                   "\}\);/s";
        
        $newContent = preg_replace($pattern, "", $content);
        
        // Clean up multiple empty lines
        $newContent = preg_replace("/\n{3,}/", "\n\n", $newContent);
        
        // Remove trailing newlines at the end of file but keep one
        $newContent = rtrim($newContent) . "\n";
        
        if ($newContent !== $content) {
            File::put($routePath, $newContent);
            $this->line("  - Routes for '{$featureSnake}' removed from admin.php");
        } else {
            $this->line("  - No routes found for '{$featureSnake}'");
        }
    }
    
    /**
     * Drop database table if exists
     */
    protected function dropTable($featurePlural)
    {
        $this->info("Checking database table...");
        
        $tableName = "admin_{$featurePlural}";
        
        // Check if table exists
        if (Schema::hasTable($tableName)) {
            $force = $this->option('force');
            
            if ($force || $this->confirm("Drop database table '{$tableName}'?", true)) {
                // Drop the table
                Schema::dropIfExists($tableName);
                $this->line("  - Dropped table: {$tableName}");
                
                // Remove migration history from migrations table
                $this->removeMigrationHistory($tableName);
            } else {
                $this->line("  - Kept table: {$tableName}");
            }
        } else {
            $this->line("  - Table '{$tableName}' not found");
            
            // Still try to remove migration history even if table doesn't exist
            $this->removeMigrationHistory($tableName);
        }
    }
    
    /**
     * Remove migration history from migrations table
     */
    protected function removeMigrationHistory($tableName)
    {
        try {
            // Find migration records that match this table
            $migrationPattern = "%create_{$tableName}_table";
            
            $migrations = DB::table('migrations')
                ->where('migration', 'like', $migrationPattern)
                ->get();
            
            if ($migrations->count() > 0) {
                foreach ($migrations as $migration) {
                    DB::table('migrations')
                        ->where('id', $migration->id)
                        ->delete();
                    
                    $this->line("  - Removed migration history: {$migration->migration}");
                }
            } else {
                $this->line("  - No migration history found for table: {$tableName}");
            }
        } catch (\Exception $e) {
            $this->warn("  - Could not remove migration history: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up empty directories
     */
    protected function cleanupEmptyDirectories($module, $featureSnake)
    {
        $this->info("Cleaning up empty directories...");
        
        $directories = [
            base_path("jiny/{$module}/resources/views/admin/admin_{$featureSnake}"),
            base_path("jiny/{$module}/App/Http/Controllers/Admin/Admin" . Str::studly($featureSnake))
        ];
        
        foreach ($directories as $dir) {
            if (File::exists($dir) && File::isDirectory($dir)) {
                $files = File::allFiles($dir);
                if (count($files) === 0) {
                    File::deleteDirectory($dir);
                    $this->line("  - Removed empty directory: " . str_replace(base_path(), '', $dir));
                }
            }
        }
    }
}