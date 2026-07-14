<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_auth_routes_are_registered(): void
    {
        $routes = Route::getRoutes();

        $this->assertNotNull($routes->getByName('login'));
        $this->assertNotNull($routes->getByName('signup.post'));
        $this->assertNotNull($routes->getByName('password.forgot'));
        $this->assertNotNull($routes->getByName('password.reset'));
    }
}
