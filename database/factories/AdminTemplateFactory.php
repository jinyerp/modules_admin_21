<?php

namespace Jiny\Admin2\Database\Factories;

use Jiny\Admin2\App\Models\AdminTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminTemplateFactory extends Factory
{
    protected $model = AdminTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Dashboard', 'E-commerce', 'Analytics', 'CRM', 'CMS', 'Social', 'Portfolio', 'Blog'];
        $authors = ['John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Williams', 'David Brown', 'Lisa Davis'];
        
        $name = $this->faker->unique()->catchPhrase();
        $slug = \Illuminate\Support\Str::slug($name);
        
        return [
            'enable' => $this->faker->boolean(80), // 80% chance of being enabled
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement($categories),
            'version' => $this->faker->randomElement(['1.0.0', '1.1.0', '1.2.0', '2.0.0', '2.1.0', '3.0.0']),
            'author' => $this->faker->randomElement($authors),
            'settings' => [
                'theme' => $this->faker->randomElement(['light', 'dark', 'auto']),
                'layout' => $this->faker->randomElement(['fixed', 'fluid', 'boxed']),
                'sidebar' => $this->faker->randomElement(['left', 'right', 'collapsed']),
                'features' => $this->faker->randomElements(['charts', 'tables', 'forms', 'widgets', 'calendar'], 3)
            ],
        ];
    }

    /**
     * Indicate that the template is enabled.
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enable' => true,
        ]);
    }

    /**
     * Indicate that the template is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enable' => false,
        ]);
    }
}