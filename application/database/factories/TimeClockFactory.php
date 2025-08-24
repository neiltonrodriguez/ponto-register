<?php

namespace Database\Factories;

use App\Models\TimeClock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeClock>
 */
class TimeClockFactory extends Factory
{
    protected $model = TimeClock::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::inRandomOrder()->first()->id ?? User::factory(),
            'clocked_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
