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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'availability' => 'nullable|array',
            'specialization_ids' => 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
        ]);

        $imagePath = $request->hasFile('image') ? $this->storeImageFile($request->file('image')) : null;
        $validated['image'] = $imagePath; // ← ADD THIS LINE: replace the raw uploaded file object with the resolved URL

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'role' => $validated['role'],
                'image' => $imagePath,
                'password' => Hash::make('Password123!'),
                'is_approved' => true,
            ]
        );

        $staff = StaffMember::create([
            ...$validated,
            'user_id' => $user->id,
            'salary' => $validated['salary'] ?? 0,
        ]);

        if (!empty($validated['specialization_ids'])) {
            $staff->specializations()->sync($validated['specialization_ids']);
        }

        return response()->json(['message' => 'Staff member added.', 'data' => $staff->load('specializations')], 201);
    }

    public function updateStaff(Request $request, StaffMember $staffMember)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:staff_members,email,' . $staffMember->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|required|string',
            'salary' => 'nullable|numeric',
            'bio' => 'nullable|string',
            'specialty' => 'nullable|string',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'availability' => 'nullable|array',
            'specialization_ids' => 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
        ]);

        $imagePath = $request->hasFile('image') ? $this->storeImageFile($request->file('image')) : null;
        if ($imagePath) {
            $validated['image'] = $imagePath;
        }

        $staffMember->update($validated);

        if ($staffMember->user) {
            $staffMember->user->update([
                'name' => $validated['name'] ?? $staffMember->user->name,
                'email' => $validated['email'] ?? $staffMember->user->email,
                'phone' => $validated['phone'] ?? $staffMember->user->phone,
                'role' => $validated['role'] ?? $staffMember->user->role,
                'image' => $validated['image'] ?? $staffMember->user->image,
                'is_approved' => true,
            ]);
        }

        if (array_key_exists('specialization_ids', $validated)) {
            $staffMember->specializations()->sync($validated['specialization_ids']);
        }

        return response()->json(['message' => 'Staff member updated.', 'data' => $staffMember->fresh()->load('specializations')]);
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
            'role' => 'required|string|in:patient,doctor,technician,admin,staff',
            'password' => 'required|string|min:8',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_approved' => 'nullable|boolean',
        ]);

        $imagePath = $request->hasFile('image') ? $this->storeImageFile($request->file('image')) : null;
        if (! $imagePath && ! empty($validated['image']) && is_string($validated['image'])) {
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
}
