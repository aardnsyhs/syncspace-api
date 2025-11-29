<?php

use App\Models\User;

test('user can register', function () {
  $response = $this->postJson('/api/register', [
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => 'Password123',
    'password_confirmation' => 'Password123',
  ]);

  $response->assertStatus(201)
    ->assertJsonStructure([
      'data' => ['id', 'name', 'email'],
      'token',
    ]);

  $this->assertDatabaseHas('users', [
    'email' => 'testuser@example.com',
  ]);
});

test('user can login', function () {
  $user = User::factory()->create([
    'password' => 'password123',
  ]);

  $response = $this->postJson('/api/login', [
    'email' => $user->email,
    'password' => 'password123',
  ]);

  $response->assertOk()
    ->assertJsonStructure([
      'data' => ['id', 'name', 'email'],
      'token',
    ]);
});

test('user cannot login with wrong password', function () {
  $user = User::factory()->create();

  $response = $this->postJson('/api/login', [
    'email' => $user->email,
    'password' => 'wrong-password',
  ]);

  $response->assertStatus(422)
    ->assertJsonValidationErrors(['email']);
});

test('authenticated user can get their profile', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)
    ->getJson('/api/user');

  $response->assertOk()
    ->assertJson([
      'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
      ],
    ]);
});

test('unauthenticated user cannot access protected routes', function () {
  $response = $this->getJson('/api/user');

  $response->assertStatus(401);
});

test('user can logout', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test-token')->plainTextToken;

  $response = $this->withHeader('Authorization', "Bearer {$token}")
    ->postJson('/api/logout');

  $response->assertOk()
    ->assertJson(['message' => 'Logged out successfully.']);

  // Token should be revoked
  $this->assertDatabaseMissing('personal_access_tokens', [
    'tokenable_id' => $user->id,
  ]);
});
