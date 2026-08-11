@extends('layout.base')

@section('page_title', 'Password Reset')

@section('page_content')

    <!-- ===== FORGOT PASSWORD SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Reset Your Password</h2>
                <p class="auth-subtitle">Enter the email linked to your Maraba Charity Hospital account and we will send a secure reset link.</p>

                @if(session('status'))
                    <div class="auth-feedback success">{{ session('status') }}</div>
                @endif
                @error('email')
                    <div class="auth-feedback error">{{ $message }}</div>
                @enderror

                <form id="forgotPasswordForm" method="POST" action="{{ route('password.forgot') }}">
                    @csrf
                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="resetEmail" name="email" placeholder=" " value="{{ old('email') }}" required>
                        <label for="resetEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3" id="sendResetBtn">
                        <i class="bi bi-envelope-paper me-2"></i><span id="sendResetBtnText">Send Reset Link</span>
                    </button>

                    <div class="auth-footer-links">
                        Remember your password? <a href="{{ route('login') }}">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Forgot Password End -->

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