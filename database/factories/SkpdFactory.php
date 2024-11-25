<?php

namespace Database\Factories;

use App\Models\Skpd;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkpdFactory extends Factory
{
    protected $model = Skpd::class;

    public function definition()
    {
        return [
            'nosptpd' => str_pad($this->faker->numberBetween(1, 9999999999999999999), 19, '0', STR_PAD_LEFT),
            'npwpd' => $this->faker->numerify('################'),
            'masapajak' => now()->format('Y-m'),
            'nilaipajak' => $this->faker->numberBetween(100000, 1000000),
        ];
    }
}
