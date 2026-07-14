<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    public function test_password_reset_flow_succeeds_when_token_is_not_overwritten(): void
    {
        $user = User::factory()->create([
            'email' => 'reset-user@example.com',
            'password' => 'old-password-123',
        ]);

        Mail::shouldReceive('send')->andThrow(new \Exception('mail unavailable'));

        $forgotResponse = $this->postJson('/api/password/forgot', [
            'email' => $user->email,
        ]);

        $forgotResponse->assertOk();
        $forgotResponse->assertJsonPath('message', 'Password reset link was prepared, but email delivery is unavailable right now.');
        $token = $forgotResponse->json('token');

        $this->assertNotEmpty($token);

        $resetResponse = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $resetResponse->assertOk();
        $resetResponse->assertJsonPath('message', 'Password has been reset.');

        $this->assertTrue(auth()->attempt(['email' => $user->email, 'password' => 'new-password-123']));
    }
}
