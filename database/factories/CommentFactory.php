<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
  public function definition(): array
  {
    return [
      'card_id' => Card::factory(),
      'user_id' => User::factory(),
      'body' => fake()->paragraph(),
    ];
  }
}
