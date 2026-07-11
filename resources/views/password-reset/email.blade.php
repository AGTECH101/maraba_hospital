@extends('layout.base')

@section('page_title', 'Password Reset')

@section('page_content')

    <!-- ===== FORGOT PASSWORD SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Reset Your Password</h2>
                <p class="auth-subtitle">Enter your email address and we'll send you a link to reset your password.</p>

                <form id="forgotPasswordForm" onsubmit="handleForgotPassword(event)">

                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="resetEmail" placeholder=" " required>
                        <label for="resetEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="bi bi-envelope-paper me-2"></i>Send Reset Link
                    </button>

                    <div class="auth-footer-links">
                        Remember your password? <a href="{{ route('login') }}">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Forgot Password End -->

@endsection