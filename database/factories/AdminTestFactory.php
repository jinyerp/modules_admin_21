<?php

namespace Jiny\Admin2\Database\Factories;

use Jiny\Admin2\App\Models\AdminTest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminTestFactory extends Factory
{
    protected $model = AdminTest::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'enable' => $this->faker->boolean(),
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function enabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'enable' => true,
            ];
        });
    }

    public function disabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'enable' => false,
            ];
        });
    }

    public function withParent($parentId)
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return [
                'parent_id' => $parentId,
            ];
        });
    }
}