<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;

class HospitalController extends Controller
{
    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    public function home()
    {
        $services = $this->tableExists('services')
            ? Service::query()->where('is_active', true)->latest()->take(6)->get()
            : collect();

        $staff = $this->tableExists('staff_members')
            ? StaffMember::query()->where('role', 'doctor')->latest()->take(4)->get()
            : collect();

        return view('index', ['services' => $services, 'staff' => $staff]);
    }

    public function services()
    {
        $services = $this->tableExists('services')
            ? Service::query()->where('is_active', true)->get()
            : collect();

        return view('service', ['services' => $services]);
    }

    public function serviceDetail($slug)
    {
        if (! $this->tableExists('services')) {
            abort(404);
        }

        $service = Service::query()->where('slug', $slug)->firstOrFail();

        return view('service-detail', [
            'service' => $service,
            'services' => Service::query()->where('is_active', true)->where('id', '!=', $service->id)->take(8)->get(),
        ]);
    }

    public function team()
    {
        $staff = $this->tableExists('staff_members')
            ? StaffMember::query()->latest()->get()
            : collect();

        return view('team', ['staff' => $staff]);
    }

    public function appointment()
    {
        $services = $this->tableExists('services')
            ? Service::query()->where('is_active', true)->get()
            : collect();
        $staffMembers = $this->tableExists('staff_members')
            ? StaffMember::query()->get()
            : collect();

        return view('appointment', [
            'services' => $services,
            'staffMembers' => $staffMembers,
        ]);
    }

    public function dashboard()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        $staffMembers = $this->tableExists('staff_members')
            ? StaffMember::query()->latest()->get()
            : collect();
        $appointments = $this->tableExists('appointments')
            ? Appointment::query()->with(['service', 'staffMember', 'transaction'])->latest()->take(10)->get()
            : collect();
        $transactions = $this->tableExists('transactions')
            ? Transaction::query()->with('appointment.service')->latest()->take(10)->get()
            : collect();

        return view('admin-dashboard', [
            'stats' => [
                'services' => $this->tableExists('services') ? Service::query()->count() : 0,
                'appointments' => $this->tableExists('appointments') ? Appointment::query()->count() : 0,
                'staff' => $staffMembers->count(),
                'transactions' => $this->tableExists('transactions') ? Transaction::query()->count() : 0,
            ],
            'staffMembers' => $staffMembers,
            'appointments' => $appointments,
            'transactions' => $transactions,
        ]);
    }

    public function staffDashboard()
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, ['doctor', 'technician', 'admin'], true)) {
            abort(403);
        }

        $staffMember = $this->tableExists('staff_members') ? StaffMember::query()->latest()->first() : null;

        return view('staff-dashboard', [
            'staffMember' => $staffMember,
            'appointments' => $this->tableExists('appointments')
                ? Appointment::query()
                    ->when($staffMember, function ($query) use ($staffMember) {
                        return $query->where('staff_member_id', $staffMember->id);
                    })
                    ->with(['service', 'staffMember'])
                    ->latest()
                    ->take(10)
                    ->get()
                : collect(),
        ]);
    }

    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $userId = null;
        if (!empty($validated['patient_email'])) {
            $user = User::firstOrCreate(
                ['email' => $validated['patient_email']],
                ['name' => $validated['patient_name'], 'password' => Str::random(12)]
            );
            $userId = $user->id;
        }

        $appointment = Appointment::create([
            ...$validated,
            'user_id' => $userId,
            'confirmation_code' => 'APT-' . strtoupper(Str::random(8)),
            'amount' => $service->price,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'TXN-' . strtoupper(Str::random(8)),
            'invoice_number' => 'INV-' . $appointment->id,
            'payment_method' => 'card',
            'status' => 'paid',
            'amount' => $service->price + 120,
            'meta' => ['gateway' => 'local'],
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully.',
            'data' => $appointment->load(['service', 'transaction'])
        ], 201);
    }

    public function listServices()
    {
        $services = $this->tableExists('services')
            ? Service::query()->where('is_active', true)->with('specializations')->get()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'price' => (float) $service->price,
                    'icon' => $service->icon,
                    'specialization_ids' => $service->specializations->pluck('id')->toArray(),
                ];
            })
            : collect();

        return response()->json([
            'data' => $services,
        ]);
    }

    public function listStaff()
    {
        $staffMembers = $this->tableExists('staff_members')
            ? StaffMember::query()->with('specializations')->get()->map(function ($staffMember) {
                return [
                    'id' => $staffMember->id,
                    'name' => $staffMember->name,
                    'role' => $staffMember->role,
                    'specialty' => $staffMember->specialty,
                    'bio' => $staffMember->bio,
                    'image' => $staffMember->image,
                    'availability' => $staffMember->availability,
                    'specialization_ids' => $staffMember->specializations->pluck('id')->toArray(),
                ];
            })
            : collect();

        return response()->json([
            'data' => $staffMembers,
        ]);
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        return response()->json([
            'message' => 'Message received. We will get back to you shortly.',
            'data' => $validated,
        ], 201);
    }
}
