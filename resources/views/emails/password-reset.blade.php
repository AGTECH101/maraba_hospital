<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset your password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <h2 style="color: #0f766e;">Reset your Maraba Hospital password</h2>
        <p>Hello {{ $user->name ?? $user->email }},</p>
        <p>We received a request to reset your password for your Maraba Hospital account.</p>
        <p><a href="{{ $resetUrl }}" style="display: inline-block; padding: 10px 16px; background: #0f766e; color: #ffffff; text-decoration: none; border-radius: 6px;">Reset Password</a></p>
        <p>If you did not request this, you can ignore this email.</p>
    </div>
</body>
</html>
