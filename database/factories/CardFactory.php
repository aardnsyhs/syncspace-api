<?php

namespace Database\Factories;

use App\Models\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
  public function definition(): array
  {
    return [
      'column_id' => Column::factory(),
      'title' => fake()->sentence(4),
      'description' => fake()->optional()->paragraph(),
      'position' => 0,
      'assignee_id' => null,
      'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
    ];
  }

  public function withAssignee(?User $user = null): static
  {
    return $this->state(fn() => [
      'assignee_id' => $user?->id ?? User::factory(),
    ]);
  }
}
