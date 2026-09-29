<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Devotion>
 */
class DevotionFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => 'devotion',
            'image' => null,
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>',
        ];
    }
}
