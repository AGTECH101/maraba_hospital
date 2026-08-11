<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Specialization;
use App\Models\StaffMember;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function resolveStorageDisk(): string
    {
        $useCloudinary = filter_var(env('SWITCH_TO_CLOUDINARY', false), FILTER_VALIDATE_BOOLEAN);

        return $useCloudinary ? 'cloudinary' : (env('FILESYSTEM_DISK', 'public') === 'local' ? 'public' : env('FILESYSTEM_DISK', 'public'));
    }

    protected function buildImageUrl(string $path, string $disk): ?string
    {
        try {
            $adapter = Storage::disk($disk);

            if (method_exists($adapter, 'url')) {
                return $adapter->url($path);
            }
        } catch (\Throwable $e) {
            Log::warning('Unable to resolve image URL for disk ' . $disk . ': ' . $e->getMessage());
        }

        return rtrim(config('app.url', 'http://localhost'), '/') . '/storage/' . ltrim($path, '/');
    }

    protected function storeImageFile($image, string $folder = 'profile-images'): ?string
    {
        if (! $image || ! $image->isValid()) {
            return null;
        }

        $disk = $this->resolveStorageDisk();
        $path = $image->store($folder, ['disk' => $disk]);

        return $path ? $this->buildImageUrl($path, $disk) : null;
    }

    protected function storeBase64Image($imageData): ?string
    {
        if (! $imageData || ! is_string($imageData) || strpos($imageData, 'data:image') !== 0) {
            return null;
        }

        try {
            $parts = explode(',', $imageData);
            if (count($parts) !== 2) {
                return null;
            }

            $data = base64_decode($parts[1], true);
            if ($data === false) {
                return null;
            }

            $disk = $this->resolveStorageDisk();
            $filename = 'profile-' . time() . '-' . uniqid() . '.png';
            $path = 'profile-images/' . $filename;
            Storage::disk($disk)->put($path, $data);

            return $this->buildImageUrl($path, $disk);
        } catch (\Throwable $e) {
            Log::warning('Base64 image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    // ===== STATS & DATA =====
    public function stats()
    {
        return response()->json([
            'data' => [
                'services' => $this->tableExists('services') ? Service::query()->count() : 0,
                'appointments' => $this->tableExists('appointments') ? Appointment::query()->count() : 0,
                'staff' => $this->tableExists('staff_members') ? StaffMember::query()->count() : 0,
                'transactions' => $this->tableExists('transactions') ? Transaction::query()->count() : 0,
            ],
        ]);
    }

    public function data()
    {
        return response()->json([
            'stats' => [
                'services' => $this->tableExists('services') ? Service::query()->count() : 0,
                'appointments' => $this->tableExists('appointments') ? Appointment::query()->count() : 0,
                'staff' => $this->tableExists('staff_members') ? StaffMember::query()->count() : 0,
                'transactions' => $this->tableExists('transactions') ? Transaction::query()->count() : 0,
            ],
            'staff' => $this->tableExists('staff_members') ? StaffMember::query()->with(['specializations', 'appointments'])->latest()->get() : collect(),
            'appointments' => $this->tableExists('appointments') ? Appointment::query()->with(['service', 'staffMember', 'transaction'])->latest()->get() : collect(),
            'transactions' => $this->tableExists('transactions') ? Transaction::query()->with('appointment.service')->latest()->get() : collect(),
            'specializations' => $this->tableExists('specializations') ? Specialization::query()->with(['services', 'staffMembers'])->latest()->get() : collect(),
        ]);
    }

    public function staff()
    {
        return response()->json([
            'data' => $this->tableExists('staff_members') ? StaffMember::query()->with(['specializations', 'appointments'])->latest()->get() : collect(),
        ]);
    }

    public function appointments()
    {
        return response()->json([
            'data' => $this->tableExists('appointments') ? Appointment::query()->with(['service', 'staffMember', 'transaction'])->latest()->get() : collect(),
        ]);
    }

    public function transactions()
    {
        return response()->json([
            'data' => $this->tableExists('transactions') ? Transaction::query()->with('appointment.service')->latest()->get() : collect(),
        ]);
    }

    // ===== STAFF MANAGEMENT =====

    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string',
            'salary' => 'nullable|numeric',
            'bio' => 'nullable|string',
            'specialty' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'image' => ['nullable'],
            'availability' => 'nullable|array',
            'specialization_ids' => 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
        ]);

        // Handle image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->storeImageFile($request->file('image'));
        } elseif ($request->filled('image') && is_string($request->input('image')) && strpos($request->input('image'), 'data:image') === 0) {
            $imagePath = $this->storeBase64Image($request->input('image'));
        }

        // Create or update the user – explicitly hash the password
        $plainPassword = $validated['password'] ?? 'mbh_password123';
        $hashedPassword = Hash::make($plainPassword);

        $user = User::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'role' => $validated['role'],
                'is_approved' => true,
                'password' => $hashedPassword,
                'image' => $imagePath,
            ]
        );

        Log::info('StoreStaff: User created/updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'password_hash' => $user->password,
        ]);

        // Build staff data
        $staffData = [
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'salary' => $validated['salary'] ?? 0,
            'bio' => $validated['bio'] ?? null,
            'specialty' => $validated['specialty'] ?? null,
            'image' => $imagePath,
            'availability' => $validated['availability'] ?? null,
        ];

        $staff = StaffMember::create($staffData);

        if (!empty($validated['specialization_ids'])) {
            $staff->specializations()->sync($validated['specialization_ids']);
        }

        return response()->json([
            'message' => 'Staff member added.',
            'data' => $staff->load('specializations'),
            'user' => $user->only(['id', 'email', 'password']), // debug
        ], 201);
    }

    public function updateStaff(Request $request, StaffMember $staffMember)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:staff_members,email,' . $staffMember->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|required|string',
            'password' => 'nullable|string|min:6',
            'salary' => 'nullable|numeric',
            'bio' => 'nullable|string',
            'specialty' => 'nullable|string',
            'image' => ['nullable'],
            'availability' => 'nullable|array',
            'specialization_ids' => 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
        ]);

        // Handle image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->storeImageFile($request->file('image'));
        } elseif ($request->filled('image') && is_string($request->input('image')) && strpos($request->input('image'), 'data:image') === 0) {
            $imagePath = $this->storeBase64Image($request->input('image'));
        }

        // Build staff update data
        $staffData = [];
        $allowedFields = ['name', 'email', 'phone', 'role', 'salary', 'bio', 'specialty', 'availability'];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $validated)) {
                $staffData[$field] = $validated[$field];
            }
        }
        if ($imagePath) {
            $staffData['image'] = $imagePath;
        }

        // Update staff record
        $staffMember->update($staffData);

        // Ensure a linked user exists
        if (! $staffMember->user) {
            Log::warning('Staff member has no linked user, creating one', ['staff_id' => $staffMember->id]);
            $newUser = User::updateOrCreate(
                ['email' => $staffMember->email],
                [
                    'name' => $staffMember->name,
                    'phone' => $staffMember->phone,
                    'role' => $staffMember->role,
                    'is_approved' => true,
                    'password' => Hash::make('mbh_password123'),
                ]
            );
            $staffMember->update(['user_id' => $newUser->id]);
            $staffMember->refresh();
        }

        // Now update the user
        if ($staffMember->user) {
            $user = $staffMember->user;

            // Update basic info
            $user->name = $validated['name'] ?? $user->name;
            $user->email = $validated['email'] ?? $user->email;
            $user->phone = $validated['phone'] ?? $user->phone;
            $user->role = $validated['role'] ?? $user->role;
            $user->image = $imagePath ?? $user->image;
            $user->is_approved = true;

            // If a new password is provided, hash and set it
            if (!empty($validated['password'])) {
                $plainPassword = $validated['password'];
                $user->password = Hash::make($plainPassword);
                Log::info('updateStaff: Setting new password hash', [
                    'user_id' => $user->id,
                    'plain' => '****',
                    'hash' => $user->password,
                ]);
            } else {
                Log::info('updateStaff: No password change provided', ['user_id' => $user->id]);
            }

            // Save the user
            $user->save();

            Log::info('updateStaff: User saved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'password_hash' => $user->password,
            ]);
        } else {
            Log::error('updateStaff: No user found after ensuring existence', ['staff_id' => $staffMember->id]);
        }

        // Sync specializations
        if (array_key_exists('specialization_ids', $validated)) {
            $staffMember->specializations()->sync($validated['specialization_ids']);
        }

        return response()->json([
            'message' => 'Staff member updated.',
            'data' => $staffMember->fresh()->load('specializations'),
            'user' => $staffMember->user ? $staffMember->user->only(['id', 'email', 'password']) : null,
        ]);
    }

    public function updateStaffBio(Request $request, StaffMember $staffMember)
    {
        $validated = $request->validate([
            'bio' => 'required|string|max:2000',
        ]);

        $staffMember->update($validated);

        return response()->json(['message' => 'Bio updated.', 'data' => $staffMember->fresh()]);
    }

    public function updateStaffAvailability(Request $request, StaffMember $staffMember)
    {
        $validated = $request->validate([
            'availability' => 'required|array',
        ]);

        $staffMember->update($validated);

        return response()->json(['message' => 'Availability updated.', 'data' => $staffMember->fresh()]);
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $appointment->update($validated);

        return response()->json(['message' => 'Appointment status updated.', 'data' => $appointment->fresh()]);
    }

    // ===== USER MANAGEMENT (pending approvals) =====
    public function pendingUsers()
    {
        $users = User::query()->where('is_approved', false)->latest()->get(['id', 'name', 'email', 'phone', 'created_at']);

        return response()->json(['data' => $users]);
    }

    public function approveUser(User $user)
    {
        $user->update(['is_approved' => true]);

        return response()->json(['message' => 'User approved.', 'data' => $user->fresh()]);
    }

    public function declineUser(User $user)
    {
        $user->forceDelete();

        return response()->json(['message' => 'User declined.']);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:doctor,technician,admin,staff,owner',
            'password' => 'required|string|min:8',
            'image' => ['nullable'],
            'is_approved' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->storeImageFile($request->file('image'));
        } elseif (!empty($validated['image']) && is_string($validated['image'])) {
            $imagePath = $this->storeBase64Image($validated['image']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'image' => $imagePath,
            'is_approved' => $validated['is_approved'] ?? true,
        ]);

        if ($validated['role'] !== 'patient' && $validated['role'] !== 'admin') {
            StaffMember::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $validated['role'],
            ]);
        }

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $user->fresh(['staffMember']),
        ], 201);
    }

    // ===== SPECIALIZATIONS =====
    public function indexSpecializations()
    {
        return response()->json([
            'data' => Specialization::query()->with(['services', 'staffMembers'])->latest()->get(),
        ]);
    }

    public function storeSpecialization(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:specializations,slug',
            'description' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $specialization = Specialization::create($validated);

        if (!empty($validated['service_ids'])) {
            $specialization->services()->sync($validated['service_ids']);
        }

        return response()->json(['message' => 'Specialization created.', 'data' => $specialization->load(['services', 'staffMembers'])], 201);
    }

    public function updateSpecialization(Request $request, Specialization $specialization)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|unique:specializations,slug,' . $specialization->id,
            'description' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $specialization->update($validated);

        if (array_key_exists('service_ids', $validated)) {
            $specialization->services()->sync($validated['service_ids']);
        }

        return response()->json(['message' => 'Specialization updated.', 'data' => $specialization->fresh()->load(['services', 'staffMembers'])]);
    }

    public function destroySpecialization(Specialization $specialization)
    {
        $specialization->forceDelete();

        return response()->json(['message' => 'Specialization deleted.']);
    }

    // ===== PASSWORD CHANGE FOR STAFF (self-service) =====
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
    }
}