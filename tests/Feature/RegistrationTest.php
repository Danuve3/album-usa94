<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/registro');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/registro', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect(route('album'));
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->from('/registro')->post('/registro', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/registro');
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->from('/registro')->post('/registro', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/registro');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->from('/registro')->post('/registro', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/registro');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_requires_password_minimum_length(): void
    {
        $response = $this->from('/registro')->post('/registro', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect('/registro');
        $response->assertSessionHasErrors(['password']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->from('/registro')->post('/registro', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertRedirect('/registro');
        $response->assertSessionHasErrors(['password']);
    }

    public function test_password_is_hashed_on_registration(): void
    {
        $this->post('/registro', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_authenticated_users_are_redirected_from_registration(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/registro');

        $response->assertRedirect('/');
    }
}
