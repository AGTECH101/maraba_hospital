<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_creates_a_user_with_hashed_password_and_staff_record_for_doctors(): void
    {
        $response = $this->post('/signup', [
            'name' => 'Dr. Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '08012345678',
            'role' => 'doctor',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/login');

        $user = User::query()->where('email', 'ada@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertFalse($user->is_approved);
        $this->assertDatabaseHas('staff_members', ['user_id' => $user->id, 'role' => 'doctor']);
    }
}
