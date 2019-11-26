<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function testHomePageRedirectsToLoginWhenNotLoggedIn()
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }

    public function testHomePageReturns200StatusWhenLoggedIn()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertStatus(200)
            ->assertViewIs('home');
    }
}
