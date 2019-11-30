<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginAndRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testFailedLoginShowsValidationErrors()
    {
        $response = $this->post(route('login'), []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function testFailedRegistrationShowsValidationErrors()
    {
        $response = $this->post(route('register'), []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['email', 'username', 'password']);
    }

    public function testLogoutRedirectsToLoginForm()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertStatus(302);
    }

    public function testLoginRouteRedirectsToHomePageWhenLoggedIn()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('home'));
    }

    public function testUserRedirectedAfterSuccessfulLogin()
    {
        $user = factory(User::class)->create([
            'password'              => Hash::make('12345678'),
        ]);

        $response = $this->post(route('login'), [
            'email'                 => $user->email,
            'password'              => '12345678',
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('home'));
    }

    public function testUserRedirectedAfterSuccessfulRegistration()
    {
        $response = $this->post(route('register'), [
            'email'                 => 'someone@example.com',
            'username'              => 'Someone',
            'password'              => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('home'));
    }
}
