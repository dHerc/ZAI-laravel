<?php

namespace Database\Factories;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->addDays($this->faker->numberBetween(-15, 15));
        $end = $start->copy()->addDays($this->faker->numberBetween(0, 30));
        return [
            'name' => $this->faker->name,
            'start_date' => $start,
            'end_date' => $end,
            'description' => $this->faker->text
        ];
    }
}
