<?php

namespace Database\Factories;

use App\Models\WajibPajak;
use Illuminate\Database\Eloquent\Factories\Factory;

class WajibPajakFactory extends Factory
{
    protected $model = WajibPajak::class;

    public function definition()
    {
        return [
            'jenisw' => $this->faker->randomElement(['badanUsaha', 'individual']),
            'namawpd' => $this->faker->company,
        ];
    }
}
