<?php

use App\Models\User;

test('registration requires email verification before issuing a token', function () {
  $response = $this->postJson('/api/register', [
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => 'Password123',
    'password_confirmation' => 'Password123',
  ]);

  $response->assertStatus(201)
    ->assertJsonPath('data.email', 'testuser@example.com')
    ->assertJsonPath('data.requires_verification', true)
    ->assertJsonMissingPath('token');

  $this->assertDatabaseHas('users', [
    'email' => 'testuser@example.com',
    'email_verified_at' => null,
  ]);

  $this->assertDatabaseHas('email_verifications', [
    'email' => 'testuser@example.com',
    'verified_at' => null,
  ]);

  $this->assertDatabaseMissing('personal_access_tokens', [
    'tokenable_id' => User::where('email', 'testuser@example.com')->sole()->id,
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
