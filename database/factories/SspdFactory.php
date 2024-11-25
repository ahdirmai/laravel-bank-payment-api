<?php

namespace Database\Factories;

use App\Models\Sspd;
use Illuminate\Database\Eloquent\Factories\Factory;

class SspdFactory extends Factory
{
    protected $model = Sspd::class;

    public function definition()
    {
        return [
            'nosptpd' => str_pad($this->faker->numberBetween(1, 9999999999999999999), 19, '0', STR_PAD_LEFT),
            'tglbayar' => now(),
            'jumlahbayar' => $this->faker->numberBetween(100000, 1000000),
            'modebayar' => 'Bank',
            'kasir' => 'Bank NTT',
            'tglinput' => now(),
        ];
    }
}
