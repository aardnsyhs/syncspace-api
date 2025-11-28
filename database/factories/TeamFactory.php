<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
  public function definition(): array
  {
    $name = fake()->company();

    return [
      'name' => $name,
      'slug' => Str::slug($name) . '-' . Str::random(5),
      'owner_id' => User::factory(),
    ];
  }
}
