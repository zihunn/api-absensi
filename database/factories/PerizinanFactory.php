<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perizinan>
 */
class PerizinanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $npmList = $this->faker->randomElement([
            '623C0003',
            '623C0004',
            '623C0005',
            '623C0006',
            '623C0007',
            '623C0011',
        ]);
        $npm = Str::of($npmList);

        $categoryList = $this->faker->randomElement([
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa',
        ]);
        $category = Str::of($categoryList);

        return [
            'npm' => $npm,
            'presensi_id' => $this->faker->randomNumber(4, true),
            'jadwal_id' => $this->faker->randomNumber(4, true),
            'description' => $this->faker->sentence(5),
            'category' => $category,
            'file' => 'http://127.0.0.1:8000/image/f833TZ1CjUTRRVPSD3f8sPJIen5QcJEybaMbeBRT7rxsw.jpg',
            'dosen_primary' => $this->faker->numerify('user-####'),
            'dosen_secondary' => $this->faker->numerify('user-####'),
            'read_primary' => $this->faker->boolean(),
            'read_secondary' => $this->faker->boolean(),
            'approve_by' => $this->faker->numerify('user-####'),
        ];
    }
}
