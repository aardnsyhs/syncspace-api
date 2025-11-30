<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabelFactory extends Factory
{
  public function definition(): array
  {
    return [
      'team_id' => Team::factory(),
      'name' => fake()->randomElement(['Bug', 'Feature', 'Enhancement', 'Documentation', 'Urgent', 'Low Priority']),
      'color' => fake()->hexColor(),
    ];
  }
}
