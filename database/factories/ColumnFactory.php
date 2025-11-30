<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnFactory extends Factory
{
  public function definition(): array
  {
    return [
      'board_id' => Board::factory(),
      'name' => fake()->randomElement(['To Do', 'In Progress', 'Review', 'Done']),
      'position' => 0,
    ];
  }
}
