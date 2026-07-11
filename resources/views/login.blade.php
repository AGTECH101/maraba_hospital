@extends('layout.base')

@section('page_title', 'Login')

@section('page_content')

<x-banner message="Welcome Back" page="Login" />

<!-- ===== LOGIN SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Sign In</h2>
                <p class="auth-subtitle">Don't have an account? <a href="{{ route('signup') }}">Register here</a></p>

                <form id="loginForm" onsubmit="handleLogin(event)">

                    <!-- Email -->
                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="loginEmail" placeholder=" " required>
                        <label for="loginEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="loginPassword" placeholder=" " required>
                        <label for="loginPassword"><i class="bi bi-lock me-2"></i>Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <!-- Forgot Password -->
                    <div class="forgot-password-link">
                        <a href="{{ route('password.reset.email') }}">Forgot your password?</a>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check-custom">
                        <input type="checkbox" id="rememberCheck">
                        <label for="rememberCheck">Remember me</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>

                    <div class="auth-divider">or continue with</div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2" style="border-radius:10px; border-color:#e0e0e0;">
                            <i class="fab fa-google me-2"></i>Google
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-50 py-2" style="border-radius:10px; border-color:#e0e0e0;">
                            <i class="fab fa-facebook-f me-2"></i>Facebook
                        </button>
                    </div>

                    <div class="auth-footer-links">
                        New to Maraba Hospital? <a href="{{ route('signup') }}">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Login End -->

@endsection