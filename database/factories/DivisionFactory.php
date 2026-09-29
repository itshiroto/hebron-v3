<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Division>
 */
class DivisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->words(3, true),
            'audience' => 'Remaja',
            'description' => $this->faker->sentence(),
            'image' => 'divisions-image/sample.jpg',
            'time' => 'Setiap Sabtu · 13.30 WIB',
            'short_time' => 'Sab 13.30',
            'link' => null,
            'sort' => 0,
        ];
    }
}
