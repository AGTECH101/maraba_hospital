@extends('layout.base')

@section('page_title', 'Password Reset')

@section('page_content')

<x-banner message="Reset your Password" page="Password Reset" />

    <!-- ===== CONFIRMATION SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">

                <div class="confirmation-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h2 class="auth-title">Password Reset Successful</h2>
                <p class="auth-subtitle">
                    Your password has been reset successfully.
                    <br>
                    <span class="confirmation-email" id="displayEmail"></span>
                </p>

                <div style="margin: 10px 0 25px; padding: 15px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid var(--primary); text-align: left;">
                    <p style="margin:0; font-size:14px; color:#555;">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        You can now sign in with your new password. If you didn't request this change, please contact our support team immediately.
                    </p>
                </div>

                <!-- ===== FIX: Centered button with flex ===== -->
                <a href="{{ route('login') }}" class="btn btn-primary btn-centered">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Sign In Now</span>
                </a>

                <div class="auth-footer-links">
                    <a href="{{ route('password.reset.email') }}">Request another reset</a> &middot;
                    <a href="#">Contact Support</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Confirmation End -->

@endsection