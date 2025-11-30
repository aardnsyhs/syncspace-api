<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
  public function definition(): array
  {
    return [
      'team_id' => Team::factory(),
      'name' => fake()->words(3, true),
      'description' => fake()->optional()->sentence(),
      'color' => fake()->hexColor(),
    ];
  }
}
