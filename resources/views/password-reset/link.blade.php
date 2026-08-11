@extends('layout.base')

@section('page_title', 'Password Reset')

@section('page_content')

    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Create New Password</h2>
                <p class="auth-subtitle">Choose a strong password to keep your account secure.</p>

                @if(session('status'))
                    <div class="auth-feedback success">{{ session('status') }}</div>
                @endif
                @error('email')
                    <div class="auth-feedback error">{{ $message }}</div>
                @enderror

                <form id="resetPasswordForm" method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="newPassword" name="password" placeholder=" " required minlength="8">
                        <label for="newPassword"><i class="bi bi-lock me-2"></i>New Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('newPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="confirmNewPassword" name="password_confirmation" placeholder=" " required>
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