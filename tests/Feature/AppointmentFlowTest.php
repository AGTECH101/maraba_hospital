<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_endpoint_returns_seeded_services(): void
    {
        $this->seed();

        $response = $this->getJson('/api/services');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'slug', 'description']
            ]
        ]);
    }

    public function test_appointment_can_be_created_via_api(): void
    {
        $this->seed();

        $response = $this->postJson('/api/appointments', [
            'service_id' => 1,
            'patient_name' => 'Ada Okafor',
            'patient_email' => 'ada@example.com',
            'patient_phone' => '+2348012345678',
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '09:00 AM',
            'notes' => 'Routine blood work',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.patient_name', 'Ada Okafor');
    }
}
