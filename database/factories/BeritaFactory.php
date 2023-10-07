<?php

namespace Database\Factories;

use App\Models\Berita;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Berita>
 */
class BeritaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Berita::class;

    public function definition(): array
    {
        return [
            'judul' => fake()->catchPhrase(),
            'gambar' => fake()->image(),
            'deskripsi' => fake()->paragraph(),
        ];
    }
}
