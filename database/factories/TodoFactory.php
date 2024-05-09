<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusList = $this->faker->randomElement([
            'In Progres',
            'Done',
            'Pending',
        ]);
        $status = Str::of($statusList);

        $categoryList = $this->faker->randomElement([
            'Pekerjaan',
            'Rutinitas',
            'Waktu Senggang',
            'Perkuliahan',
        ]);
        $category = Str::of($categoryList);

        return [
            'npm' => $this->faker->randomNumber(8, true),
            'title_task' => $this->faker->sentence(mt_rand(2, 8)),
            'desc_task' => $this->faker->sentence(mt_rand(3, 10)),
            'status' => $status,
            'category' => $category,
            'date' => $this->faker->date('Y-m-d'),
            'time' => $this->faker->time('H:i:s'),

        ];
    }
}
