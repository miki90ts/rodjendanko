<?php

namespace Database\Factories;

use App\Models\BirthdayContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BirthdayContact> */
class BirthdayContactFactory extends Factory
{
    protected $model = BirthdayContact::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
            'email' => fake()->safeEmail(),
            'relationship' => fake()->randomElement(['family', 'friend', 'partner', 'colleague', 'business', 'other']),
            'is_active' => true,
        ];
    }
}
