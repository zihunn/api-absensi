<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShareQr>
 */
class ShareQrFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prodiList = $this->faker->randomElement([
            'IF',
            'KEB',
            'KEP',
            'KES',
        ]);
        $prodi = Str::of($prodiList);

        return [
            'jadwal_id' => $this->faker->randomNumber(4, true),
            'presensi_id' => $this->faker->randomNumber(4, true),
            'dosen_id' => $this->faker->randomNumber(4, true),
            'nama_dosen' => $this->faker->name(),
            'file' => 'http://127.0.0.1:8000/image/f833TZ1CjUTRRVPSD3f8sPJIen5QcJEybaMbeBRT7rxsw.jpg',
            'prodi' => $prodi,
            'description' => $this->faker->text(50)
        ];

 
    }
}
