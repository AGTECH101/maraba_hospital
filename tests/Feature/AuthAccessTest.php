<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin-dashboard');

        $response->assertRedirect('/login');
    }

    public function test_auth_pages_have_named_routes(): void
    {
        $this->assertStringEndsWith('/login', route('login'));
        $this->assertStringEndsWith('/signup', route('signup'));
        $this->assertStringEndsWith('/password-reset-email', route('password.reset.email'));
        $this->assertStringEndsWith('/password-reset-link', route('password.reset.link'));
        $this->assertStringEndsWith('/password-reset-confirmation', route('password.reset.confirmation'));
    }
}
