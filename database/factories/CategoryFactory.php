<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'is_public' => $this->faker->boolean,
        ];
    }

    /**
     * Indicate category is public.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function public()
    {
        return $this->state(fn () => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate category is private.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function private()
    {
        return $this->state(fn () => [
            'is_public' => false,
        ]);
    }
}
