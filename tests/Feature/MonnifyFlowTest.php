<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonnifyFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_status_page_renders_transaction_information(): void
    {
        $service = Service::create([
            'name' => 'Cardiology Scan',
            'slug' => 'cardiology-scan',
            'description' => 'Cardiology scan',
            'long_description' => 'Long description',
            'icon' => 'bi-heart-pulse',
            'price' => 5000,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'service_id' => $service->id,
            'patient_name' => 'Ada Okafor',
            'patient_email' => 'ada@example.com',
            'patient_phone' => '+2348012345678',
            'appointment_date' => '2026-07-20',
            'appointment_time' => '10:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-TEST-123',
            'amount' => 5310,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'PAY-TEST-001',
            'invoice_number' => 'INV-001',
            'payment_method' => 'monnify',
            'status' => 'pending',
            'amount' => 5310,
            'meta' => [],
        ]);

        $response = $this->get('/payment-status?paymentReference=PAY-TEST-001');

        $response->assertOk();
        $response->assertSee('Payment Status');
        $response->assertSee('PAY-TEST-001');
    }

    public function test_return_route_redirects_to_payment_status_with_clean_reference(): void
    {
        $response = $this->get('/monnify/return?paymentReference=PAY-TEST-123%3FpaymentReference%3DPAY-TEST-123');

        $response->assertRedirectContains('/payment-status?paymentReference=PAY-TEST-123');
    }

    public function test_payment_status_page_accepts_duplicated_payment_reference(): void
    {
        $service = Service::create([
            'name' => 'Radiology Scan',
            'slug' => 'radiology-scan',
            'description' => 'Radiology scan',
            'long_description' => 'Long description',
            'icon' => 'bi-heart-pulse',
            'price' => 5000,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'service_id' => $service->id,
            'patient_name' => 'Tunde Bayo',
            'patient_email' => 'tunde@example.com',
            'patient_phone' => '+2348011111111',
            'appointment_date' => '2026-07-21',
            'appointment_time' => '11:00',
            'status' => 'pending',
            'confirmation_code' => 'APT-TEST-456',
            'amount' => 5120,
        ]);

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => 'PAY-TEST-456',
            'invoice_number' => 'INV-456',
            'payment_method' => 'monnify',
            'status' => 'pending',
            'amount' => 5120,
            'meta' => [],
        ]);

        $response = $this->get('/payment-status?paymentReference=PAY-TEST-456PAY-TEST-456');

        $response->assertOk();
        $response->assertSee('PAY-TEST-456');
    }
}
