<?php

namespace Database\Factories;

use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Election>
 */
class ElectionFactory extends Factory
{
    protected $model = Election::class;

    public function definition(): array
    {
        $start = now()->addDays($this->faker->numberBetween(-3, 3));
        $end = (clone $start)->addDays($this->faker->numberBetween(1, 7));

        return [
            'title' => 'Election ' . Str::random(5),
            'description' => $this->faker->sentence(10),
            'status' => $start->isFuture() ? 'pending' : ($end->isPast() ? 'completed' : 'active'),
            'start_date' => $start,
            'end_date' => $end,
        ];
    }
}
