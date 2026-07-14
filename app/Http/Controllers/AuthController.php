<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\StaffMember;

class AuthController extends Controller
{
    protected function storeProfileImage($image): ?string
    {
        if (! $image || ! $image->isValid()) {
            return null;
        }

        $useCloudinary = filter_var(env('SWITCH_TO_CLOUDINARY', false), FILTER_VALIDATE_BOOLEAN);
        
        if ($useCloudinary) {
            try {
                $path = \Cloudinary\Uploader::upload($image->getRealPath(), [
                    'folder' => 'maraba-hospital/profile-images',
                    'resource_type' => 'auto',
                    'quality' => 'auto',
                ])->get('secure_url');
                return $path;
            } catch (\Throwable $e) {
                \Log::warning('Cloudinary upload failed: ' . $e->getMessage());
                return null;
            }
        }
        
        return Storage::disk('local')->putFile('profile-images', $image);
    }

    protected function storeBase64Image($imageData): ?string
    {
        if (! $imageData || ! is_string($imageData) || strpos($imageData, 'data:image') !== 0) {
            return null;
        }

        try {
            $useCloudinary = filter_var(env('SWITCH_TO_CLOUDINARY', false), FILTER_VALIDATE_BOOLEAN);
            
            // Extract base64 from data URI
            $parts = explode(',', $imageData);
            if (count($parts) !== 2) {
                return null;
            }
            
            $data = base64_decode($parts[1], true);
            if ($data === false) {
                return null;
            }

            if ($useCloudinary) {
                $tempFile = tempnam(sys_get_temp_dir(), 'img');
                file_put_contents($tempFile, $data);
                
                $result = \Cloudinary\Uploader::upload($tempFile, [
                    'folder' => 'maraba-hospital/profile-images',
                    'resource_type' => 'auto',
                    'quality' => 'auto',
                ]);
                
                @unlink($tempFile);
                return $result->get('secure_url');
            }
            
            // Fallback to local storage
            $filename = 'profile-' . time() . '-' . uniqid() . '.png';
            Storage::disk('local')->put('profile-images/' . $filename, $data);
            return 'storage/profile-images/' . $filename;
        } catch (\Throwable $e) {
            \Log::warning('Base64 image upload failed: ' . $e->getMessage());
            return null;
        }
    }

public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'role' => 'required|string|in:doctor,technician',
        'password' => 'required|string|min:8|confirmed',
        'image' => 'nullable|string',
    ]);

    $imagePath = null;
    if (!empty($validated['image'])) {
        $imagePath = $this->storeBase64Image($validated['image']);
    }

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'role' => $validated['role'], // now actually uses the submitted, validated role
        'password' => $validated['password'],
        'is_approved' => false,
        'image' => $imagePath,
    ]);

    // Also create the linked StaffMember record so this user shows up
    // in the admin dashboard's staff list once approved
    StaffMember::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'role' => $user->role,
    ]);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'message' => 'Account created successfully. Pending admin approval before you can sign in.',
            'data' => $user->only(['id', 'name', 'email', 'role', 'is_approved', 'image']),
        ], 201);
    }

    return redirect('/login')->with('status', 'Account created successfully. Pending admin approval before you can sign in.');
}

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Invalid email or password.'], 401);
            }

            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        if (! $user->is_approved) {
            Auth::logout();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Your account is pending approval by an administrator.'], 403);
            }

            return back()->withErrors(['email' => 'Your account is pending approval by an administrator.'])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $redirectPath = $user->role === 'admin' ? '/admin-dashboard' : ($user->role === 'doctor' || $user->role === 'technician' ? '/staff-dashboard' : '/');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Signed in successfully.',
                'redirect' => $redirectPath,
            ]);
        }

        return redirect($redirectPath)->with('status', 'Signed in successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);
        $user = User::query()->where('email', $validated['email'])->first();
        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'No account found for that email.'], 404);
            }

            return back()->withErrors(['email' => 'No account found for that email.']);
        }

        $token = Password::createToken($user);

        $resetUrl = URL::temporarySignedRoute(
            'password.reset.link',
            now()->addHours(1),
            ['email' => $user->email, 'token' => $token]
        );

        try {
            Mail::send('emails.password-reset', ['user' => $user, 'resetUrl' => $resetUrl], function ($message) use ($user) {
                $message->to($user->email)->subject('Reset your Maraba Hospital password');
            });
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Password reset link was prepared, but email delivery is unavailable right now.',
                    'token' => $token,
                    'reset_url' => $resetUrl,
                ], 200);
            }

            return back()->with('status', 'Password reset link was prepared, but email delivery is unavailable right now.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Password reset link sent.']);
        }

        return back()->with('status', 'Please check your email for the password reset link.');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            ['email' => $validated['email'], 'token' => $validated['token'], 'password' => $validated['password']],
            function (User $user, $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Password has been reset.']);
            }

            return redirect()->route('password.reset.confirmation')->with('status', 'Password has been reset.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Invalid token or unable to reset password.'], 400);
        }

        return back()->withErrors(['email' => 'Invalid token or unable to reset password.']);
    }
}
