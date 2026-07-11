<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_requires_approval(): void
    {
        $response = $this->post('/signup', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348012345678',
            'role' => 'patient',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'is_approved' => false,
            'role' => 'patient',
        ]);
    }

    public function test_unapproved_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'pending@example.com',
            'password' => Hash::make('secret1234'),
            'is_approved' => false,
            'role' => 'patient',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'secret1234',
        ]);

        $response->assertStatus(403);
    }
}
