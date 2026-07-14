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

                @if(session('status'))
                    <div class="auth-feedback success">{{ session('status') }}</div>
                @endif
                @error('email')
                    <div class="auth-feedback error">{{ $message }}</div>
                @enderror

                <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder=" " value="{{ old('email') }}" required>
                        <label for="loginEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="loginPassword" name="password" placeholder=" " required>
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
                    <button type="submit" class="btn btn-primary w-100 py-3" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i><span id="loginBtnText">Sign In</span>
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

<style>
.auth-feedback {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.auth-feedback.error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
.auth-feedback.success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}
.auth-feedback i {
    font-size: 16px;
}
</style>

@endsection