<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sign_up_with_profile_image_and_log_in(): void
    {
        $this->withoutMiddleware();

        $response = $this->post('/signup', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '08012345678',
            'role' => 'admin',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'image' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $user = \App\Models\User::query()->where('email', 'admin@example.com')->first();
        $this->assertNotNull($user->image);

        $loginResponse = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect('/admin-dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
