<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_data_is_loaded_from_database(): void
    {
        $service = Service::create([
            'name' => 'Pathology Testing',
            'slug' => 'pathology-testing',
            'description' => 'Advanced tissue analysis',
            'icon' => 'bi-heart-pulse',
            'price' => 7000,
        ]);

        $staff = StaffMember::create([
            'name' => 'Dr. Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348012345678',
            'role' => 'doctor',
            'salary' => 450000,
            'bio' => 'Lead pathologist',
            'availability' => ['start' => '08:00', 'end' => '17:00', 'days' => ['Mon', 'Wed']],
        ]);

        $appointment = Appointment::create([
            'service_id' => $service->id,
            'staff_member_id' => $staff->id,
            'patient_name' => 'Ada Okafor',
            'patient_email' => 'ada@example.com',
            'patient_phone' => '+2348099999999',
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '09:00 AM',
            'status' => 'pending',
            'confirmation_code' => 'APT-123456',
            'amount' => 7000,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'TXN-ABC123',
            'invoice_number' => 'INV-001',
            'payment_method' => 'card',
            'status' => 'paid',
            'amount' => 7000,
            'meta' => ['gateway' => 'local'],
        ]);

        $response = $this->getJson('/api/dashboard/data');

        $response->assertOk()
            ->assertJsonPath('stats.services', 1)
            ->assertJsonPath('stats.appointments', 1)
            ->assertJsonPath('stats.staff', 1)
            ->assertJsonPath('stats.transactions', 1)
            ->assertJsonPath('staff.0.name', $staff->name)
            ->assertJsonPath('appointments.0.patient_name', $appointment->patient_name);
    }

    public function test_staff_profile_updates_persist_to_the_database(): void
    {
        $staff = StaffMember::create([
            'name' => 'Dr. John Doe',
            'email' => 'john@example.com',
            'phone' => '+2348012345678',
            'role' => 'doctor',
            'salary' => 400000,
            'bio' => 'Old bio',
            'availability' => ['start' => '08:00', 'end' => '16:00', 'days' => ['Mon']],
        ]);

        $response = $this->patchJson("/api/staff/{$staff->id}/bio", [
            'bio' => 'Updated bio from dashboard',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.bio', 'Updated bio from dashboard');

        $availabilityResponse = $this->patchJson("/api/staff/{$staff->id}/availability", [
            'availability' => [
                'start' => '10:00',
                'end' => '18:00',
                'days' => ['Mon', 'Wed', 'Fri'],
            ],
        ]);

        $availabilityResponse->assertOk()
            ->assertJsonPath('data.availability.start', '10:00');

        $this->assertDatabaseHas('staff_members', [
            'id' => $staff->id,
            'bio' => 'Updated bio from dashboard',
        ]);
    }
}
