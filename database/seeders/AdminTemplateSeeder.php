<?php

namespace Jiny\Admin2\Database\Seeders;

use Illuminate\Database\Seeder;
use Jiny\Admin2\App\Models\AdminTemplate;

class AdminTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 admin templates
        AdminTemplate::factory()
            ->count(50)
            ->create();
    }
}