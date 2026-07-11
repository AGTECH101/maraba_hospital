<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Specialization;
use App\Models\StaffMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecializationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_specializations_can_be_created_and_attached_to_staff_and_services(): void
    {
        $service = Service::create([
            'name' => 'Pathology Testing',
            'slug' => 'pathology-testing',
            'description' => 'Advanced tissue analysis',
            'icon' => 'bi-heart-pulse',
            'price' => 7000,
        ]);

        $response = $this->postJson('/api/dashboard/specializations', [
            'name' => 'Pathology',
            'slug' => 'pathology',
            'description' => 'Pathology specialist',
            'service_ids' => [$service->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Pathology');

        $specialization = Specialization::first();
        $this->assertTrue($specialization->services()->where('services.id', $service->id)->exists());

        $staff = StaffMember::create([
            'name' => 'Dr. Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348012345678',
            'role' => 'doctor',
            'salary' => 450000,
            'bio' => 'Lead pathologist',
            'availability' => ['start' => '08:00', 'end' => '17:00', 'days' => ['Mon', 'Wed']],
        ]);

        $this->patchJson("/api/dashboard/staff/{$staff->id}", [
            'specialization_ids' => [$specialization->id],
        ]);

        $this->assertTrue($staff->fresh()->specializations()->where('specializations.id', $specialization->id)->exists());
    }
}
