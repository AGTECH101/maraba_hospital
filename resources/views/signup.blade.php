@extends('layout.base')

@section('page_title', 'Signup')

@section('page_content')

<x-banner message="Get Registered" page="Signup" />

    <!-- ===== REGISTRATION SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Join Maraba Hospital</h2>
                <p class="auth-subtitle">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>

                @if(session('status'))
                    <div class="auth-feedback success">{{ session('status') }}</div>
                @endif

                <form id="registerForm" method="POST" action="{{ route('signup.post') }}">
                    @csrf

                    <!-- Profile Picture -->
                    <div class="profile-upload-wrapper">
                        <div class="profile-upload-circle" id="profileUploadCircle" onclick="document.getElementById('profileInput').click()">
                            <img id="profilePreview" src="" alt="Profile" style="display:none;">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="bi bi-camera"></i>
                                <span>Upload Photo</span>
                            </div>
                            <div class="upload-overlay">
                                <i class="bi bi-pencil"></i>
                            </div>
                        </div>
                        <input type="file" id="profileInput" name="image" accept="image/*" onchange="previewProfileImage(event)">
                        <input type="hidden" name="image" id="profileImageHidden" value="">
                        <small class="profile-upload-hint">Click the circle to upload a profile picture (optional)</small>
                    </div>

                    <!-- Full Name -->
                    <div class="form-floating-custom">
                        <input type="text" class="form-control" id="fullName" name="name" placeholder=" " value="{{ old('name') }}" required>
                        <label for="fullName"><i class="bi bi-person me-2"></i>Full Name</label>
                        @error('name')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Email -->
                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="email" name="email" placeholder=" " value="{{ old('email') }}" required>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                        @error('email')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-floating-custom">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder=" " value="{{ old('phone') }}" required>
                        <label for="phone"><i class="bi bi-phone me-2"></i>Phone Number</label>
                        @error('phone')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="form-floating-custom">
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select your role</option>
                            @foreach(($roles ?? ['doctor','technician','admin']) as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <label for="role"><i class="bi bi-briefcase me-2"></i>Select Role</label>
                        @error('role')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="password" name="password" placeholder=" " required minlength="8">
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder=" " required>
                        <label for="confirmPassword"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password_confirmation')<div class="invalid-feedback-custom" style="color:#dc3545; font-size:13px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Terms -->
                    <div class="form-check-custom">
                        <input type="checkbox" id="termsCheck" required>
                        <label for="termsCheck">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100 py-3" id="registerBtn">
                        <i class="bi bi-person-plus me-2"></i><span id="registerBtnText">Create Account</span>
                    </button>

                    <div class="auth-divider">or</div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2" style="border-radius:10px; border-color:#e0e0e0;">
                            <i class="fab fa-google me-2"></i>Google
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-50 py-2" style="border-radius:10px; border-color:#e0e0e0;">
                            <i class="fab fa-facebook-f me-2"></i>Facebook
                        </button>
                    </div>

                    <div class="auth-footer-links">
                        Already registered? <a href="{{ route('login') }}">Sign in to your account</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Registration End -->

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