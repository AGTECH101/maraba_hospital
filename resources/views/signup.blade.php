@extends('layout.base')

@section('page_title', 'Signup')

@section('page_content')

<x-banner message="Get Resigistered" page="Signup" />

    <!-- ===== REGISTRATION SECTION ===== -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-card wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="auth-title">Join Maraba Hospital</h2>
                <p class="auth-subtitle">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>

                <form id="registerForm" onsubmit="handleRegister(event)">

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
                        <input type="file" id="profileInput" accept="image/*" onchange="previewProfileImage(event)">
                        <small class="profile-upload-hint">Click the circle to upload a profile picture (optional)</small>
                    </div>

                    <!-- Full Name -->
                    <div class="form-floating-custom">
                        <input type="text" class="form-control" id="fullName" placeholder=" " required>
                        <label for="fullName"><i class="bi bi-person me-2"></i>Full Name</label>
                    </div>

                    <!-- Email -->
                    <div class="form-floating-custom">
                        <input type="email" class="form-control" id="email" placeholder=" " required>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <!-- Phone -->
                    <div class="form-floating-custom">
                        <input type="tel" class="form-control" id="phone" placeholder=" " required>
                        <label for="phone"><i class="bi bi-phone me-2"></i>Phone Number</label>
                    </div>

                    <!-- Role Selection -->
                    <label style="font-size:14px; font-weight:500; color:#555; margin-bottom:8px; display:block;">
                        <i class="bi bi-briefcase me-2"></i>I am a
                    </label>
                    <div class="role-selector" id="roleSelector">
                        <label class="role-option active" onclick="selectRole(this)">
                            <input type="radio" name="role" value="patient" checked>
                            <i class="bi bi-person"></i>
                            <span>Patient</span>
                        </label>
                        <label class="role-option" onclick="selectRole(this)">
                            <input type="radio" name="role" value="doctor">
                            <i class="bi bi-heart-pulse"></i>
                            <span>Doctor</span>
                        </label>
                        <label class="role-option" onclick="selectRole(this)">
                            <input type="radio" name="role" value="technician">
                            <i class="bi bi-lungs"></i>
                            <span>Technician</span>
                        </label>
                        <label class="role-option" onclick="selectRole(this)">
                            <input type="radio" name="role" value="admin">
                            <i class="bi bi-shield-check"></i>
                            <span>Admin</span>
                        </label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="password" placeholder=" " required minlength="8">
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating-custom">
                        <input type="password" class="form-control" id="confirmPassword" placeholder=" " required>
                        <label for="confirmPassword"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <!-- Terms -->
                    <div class="form-check-custom">
                        <input type="checkbox" id="termsCheck" required>
                        <label for="termsCheck">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100 py-3">
                        <i class="bi bi-person-plus me-2"></i>Create Account
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

@endsection