<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Avez-vous un bébé');
    }

    public function test_new_users_can_register_without_baby(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'has_baby' => '0',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'has_baby' => false,
            'baby_birth_date' => null,
        ]);
    }

    public function test_new_users_can_register_with_baby(): void
    {
        $response = $this->post('/register', [
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'has_baby' => '1',
            'baby_birth_date' => now()->subMonths(7)->toDateString(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'parent@example.com')->first();
        $this->assertTrue($user->has_baby);
        $this->assertNotNull($user->baby_birth_date);
        $this->assertNotNull($user->babyFeedingRecommendations());
    }

    public function test_baby_birth_date_is_required_when_has_baby(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'has_baby' => '1',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('baby_birth_date');
    }
}
