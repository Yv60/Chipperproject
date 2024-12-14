<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Chirp;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chirp>
 */
class ChirpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * 
     * 
     */

     protected $model = Chirp::class;


    public function definition(): array
    {
        return [
            'message' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
