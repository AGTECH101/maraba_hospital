@extends('layout.base')

@section('page_title', 'Password Reset')

@section('page_content')

    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Create New Password</h2>
                <p class="auth-subtitle">Please enter your new password below.</p>

                <div id="resetMessage"></div>

                <form id="resetPasswordForm" onsubmit="handleResetPassword(event)">

                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="newPassword" placeholder=" " required minlength="8">
                        <label for="newPassword"><i class="bi bi-lock me-2"></i>New Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('newPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="confirmNewPassword" placeholder=" " required>
                        <label for="confirmNewPassword"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmNewPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="bi bi-check-circle me-2"></i>Reset Password
                    </button>

                    <div class="auth-footer-links">
                        <a href="{{ route('login') }}">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Reset Password End -->

@endsection