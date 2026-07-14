<?php

namespace Tests\Feature;

use App\Http\Controllers\MonnifyController;
use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MonnifyControllerVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('accept', 'application/json');
        $this->withoutMiddleware();
    }

    public function test_paid_upcoming_appointment_returns_valid_state(): void
    {
        $appointment = Appointment::create([
            'patient_name' => 'Ada Lovelace',
            'patient_phone' => '+2348000000000',
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '10:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-VALID-001',
            'amount' => 7000,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'REF-VALID-001',
            'invoice_number' => 'INV-VALID-001',
            'status' => 'paid',
            'amount' => 7000,
        ]);

        $controller = new MonnifyController();
        $request = Request::create('/api/verification/appointment', 'POST', ['code' => 'APT-VALID-001']);
        $response = $controller->verifyAppointment($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('valid', json_decode($response->getContent(), true)['state']);
    }

    public function test_past_paid_appointment_returns_expired_state(): void
    {
        $appointment = Appointment::create([
            'patient_name' => 'Grace Hopper',
            'patient_phone' => '+2348000000001',
            'appointment_date' => now()->subDay()->toDateString(),
            'appointment_time' => '09:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-EXPIRED-001',
            'amount' => 7000,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'REF-EXPIRED-001',
            'invoice_number' => 'INV-EXPIRED-001',
            'status' => 'paid',
            'amount' => 7000,
        ]);

        $controller = new MonnifyController();
        $request = Request::create('/api/verification/appointment', 'POST', ['code' => 'APT-EXPIRED-001']);
        $response = $controller->verifyAppointment($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('expired', json_decode($response->getContent(), true)['state']);
    }

    public function test_unpaid_or_missing_appointment_returns_invalid_state(): void
    {
        $appointment = Appointment::create([
            'patient_name' => 'Linus Torvalds',
            'patient_phone' => '+2348000000002',
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '11:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-INVALID-001',
            'amount' => 7000,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'REF-INVALID-001',
            'invoice_number' => 'INV-INVALID-001',
            'status' => 'failed',
            'amount' => 7000,
        ]);

        $controller = new MonnifyController();
        $request = Request::create('/api/verification/appointment', 'POST', ['code' => 'APT-INVALID-001']);
        $response = $controller->verifyAppointment($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('invalid', json_decode($response->getContent(), true)['state']);

        $missingRequest = Request::create('/api/verification/appointment', 'POST', ['code' => 'DOES-NOT-EXIST']);
        $missingResponse = $controller->verifyAppointment($missingRequest);

        $this->assertSame(200, $missingResponse->getStatusCode());
        $this->assertSame('invalid', json_decode($missingResponse->getContent(), true)['state']);
    }

    public function test_mark_used_endpoint_sets_used_state(): void
    {
        $appointment = Appointment::create([
            'patient_name' => 'Margaret Hamilton',
            'patient_phone' => '+2348000000003',
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '15:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-USED-001',
            'amount' => 7000,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'REF-USED-001',
            'invoice_number' => 'INV-USED-001',
            'status' => 'paid',
            'amount' => 7000,
        ]);

        $controller = new MonnifyController();
        $markRequest = Request::create('/api/verification/mark-used', 'POST', ['code' => 'APT-USED-001']);
        $markResponse = $controller->markAppointmentUsed($markRequest);

        $this->assertSame(200, $markResponse->getStatusCode());
        $this->assertSame('Appointment marked as used.', json_decode($markResponse->getContent(), true)['message']);

        $appointment->refresh();
        $this->assertNotNull($appointment->used_at);

        $verifyRequest = Request::create('/api/verification/appointment', 'POST', ['code' => 'APT-USED-001']);
        $response = $controller->verifyAppointment($verifyRequest);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('used', json_decode($response->getContent(), true)['state']);
    }
}
