<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:patient,doctor,technician,admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'patient',
            'password' => $validated['password'],
            'is_approved' => false,
        ]);

        return response()->json([
            'message' => 'Account created. Your access will be approved by an administrator before you can sign in.',
            'data' => $user->only(['id', 'name', 'email', 'role', 'is_approved']),
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        if (! $user->is_approved) {
            Auth::logout();
            return response()->json(['message' => 'Your account is pending approval by an administrator.'], 403);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Signed in successfully.',
            'redirect' => $user->role === 'admin' ? '/admin-dashboard' : ($user->role === 'doctor' || $user->role === 'technician' ? '/staff-dashboard' : '/'),
        ]);
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
            return response()->json(['message' => 'No account found for that email.'], 404);
        }

        $token = Password::createToken($user);

        DB::table(config('auth.passwords.users.table', 'password_reset_tokens'))->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

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
            return response()->json([
                'message' => 'Password reset link was prepared, but email delivery is unavailable right now.',
                'token' => $token,
                'reset_url' => $resetUrl,
            ], 200);
        }

        return response()->json(['message' => 'Password reset link sent.']);
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
            return response()->json(['message' => 'Password has been reset.']);
        }

        return response()->json(['message' => 'Invalid token or unable to reset password.'], 400);
    }
}
