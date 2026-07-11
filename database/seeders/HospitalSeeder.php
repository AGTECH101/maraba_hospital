<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Pathology Testing', 'slug' => 'pathology-testing', 'description' => 'Advanced tissue and cellular analysis for accurate diagnosis.', 'icon' => 'bi-heart-pulse', 'price' => 7000],
            ['name' => 'Microbiology Tests', 'slug' => 'microbiology-tests', 'description' => 'Cultures and rapid analysis for bacteria, viruses, and fungi.', 'icon' => 'bi-lungs', 'price' => 7500],
            ['name' => 'Biochemistry Tests', 'slug' => 'biochemistry-tests', 'description' => 'Metabolic and chemical analysis to support treatment planning.', 'icon' => 'bi-virus', 'price' => 6500],
            ['name' => 'Blood Tests', 'slug' => 'blood-tests', 'description' => 'Complete blood count, typing, and infection screening.', 'icon' => 'bi-prescription2', 'price' => 6000],
            ['name' => 'Urine Tests', 'slug' => 'urine-tests', 'description' => 'Comprehensive urinalysis and culture services.', 'icon' => 'bi-capsule', 'price' => 5000],
        ];

        $createdServices = collect();
        foreach ($services as $service) {
            $createdServices->push(Service::create($service));
        }

        $staff = [
            ['name' => 'Dr. James Okafor', 'email' => 'james@marabahospital.com', 'phone' => '+2348034567890', 'role' => 'doctor', 'specialty' => 'Lead Pathologist', 'bio' => 'Board-certified pathologist with a calm and compassionate approach.', 'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=800&q=80', 'salary' => 450000, 'availability' => ['start' => '08:00', 'end' => '17:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']]],
            ['name' => 'Dr. Amara Okoro', 'email' => 'amara@marabahospital.com', 'phone' => '+2347037894521', 'role' => 'doctor', 'specialty' => 'Senior Pathologist', 'bio' => 'Specialist in clinical pathology and patient education.', 'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80', 'salary' => 420000, 'availability' => ['start' => '08:00', 'end' => '16:00', 'days' => ['Mon','Wed','Fri']]],
            ['name' => 'Dr. Chisom Eze', 'email' => 'chisom@marabahospital.com', 'phone' => '+2349051234567', 'role' => 'doctor', 'specialty' => 'Microbiology Specialist', 'bio' => 'Focused on infection control and laboratory workflow.', 'image' => 'https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&fit=crop&w=800&q=80', 'salary' => 400000, 'availability' => ['start' => '09:00', 'end' => '15:00', 'days' => ['Tue','Thu']]],
            ['name' => 'Nurse Ada Musa', 'email' => 'ada@marabahospital.com', 'phone' => '+2348123456789', 'role' => 'nurse', 'specialty' => 'Patient Care', 'bio' => 'Warm and attentive nurse who coordinates patient support.', 'image' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=800&q=80', 'salary' => 280000, 'availability' => ['start' => '07:00', 'end' => '15:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']]],
            ['name' => 'Lab Technician Tunde Adebayo', 'email' => 'tunde@marabahospital.com', 'phone' => '+2349012345678', 'role' => 'technician', 'specialty' => 'Sample Processing', 'bio' => 'Experienced technician handling sample intake and reporting.', 'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=800&q=80', 'salary' => 260000, 'availability' => ['start' => '08:30', 'end' => '17:00', 'days' => ['Mon','Wed','Fri']]],
        ];

        $createdStaff = collect();
        foreach ($staff as $member) {
            $createdStaff->push(StaffMember::create($member));
        }

        $patients = [
            ['name' => 'Ada Okafor', 'email' => 'ada.okafor@example.com', 'phone' => '+2348012345678', 'service_id' => $createdServices->get(0)->id, 'staff_member_id' => $createdStaff->get(0)->id, 'status' => 'pending', 'notes' => 'First-time patient', 'amount' => 7120],
            ['name' => 'Bola Adeyemi', 'email' => 'bola@example.com', 'phone' => '+2348023456789', 'service_id' => $createdServices->get(1)->id, 'staff_member_id' => $createdStaff->get(1)->id, 'status' => 'confirmed', 'notes' => 'Routine checkup', 'amount' => 7620],
            ['name' => 'Chinedu Nwosu', 'email' => 'chinedu@example.com', 'phone' => '+2348034567890', 'service_id' => $createdServices->get(2)->id, 'staff_member_id' => $createdStaff->get(2)->id, 'status' => 'pending', 'notes' => 'Follow-up sample', 'amount' => 6620],
            ['name' => 'Fatima Yusuf', 'email' => 'fatima@example.com', 'phone' => '+2348045678901', 'service_id' => $createdServices->get(3)->id, 'staff_member_id' => $createdStaff->get(3)->id, 'status' => 'confirmed', 'notes' => 'Needs quick turnaround', 'amount' => 6120],
            ['name' => 'Ibrahim Salihu', 'email' => 'ibrahim@example.com', 'phone' => '+2348056789012', 'service_id' => $createdServices->get(4)->id, 'staff_member_id' => $createdStaff->get(4)->id, 'status' => 'completed', 'notes' => 'Annual screening', 'amount' => 5120],
        ];

        $appointments = collect();
        foreach ($patients as $index => $patient) {
            $appointment = Appointment::create([
                'service_id' => $patient['service_id'],
                'staff_member_id' => $patient['staff_member_id'],
                'patient_name' => $patient['name'],
                'patient_email' => $patient['email'],
                'patient_phone' => $patient['phone'],
                'appointment_date' => now()->addDays($index + 1)->toDateString(),
                'appointment_time' => sprintf('%02d:00', 8 + $index),
                'notes' => $patient['notes'],
                'status' => $patient['status'],
                'confirmation_code' => 'APT-' . strtoupper(substr(md5($patient['email']), 0, 8)),
                'amount' => $patient['amount'],
            ]);

            $appointments->push($appointment);

            Transaction::create([
                'appointment_id' => $appointment->id,
                'transaction_reference' => 'TXN-' . strtoupper(substr(md5($appointment->id . $patient['email']), 0, 8)),
                'invoice_number' => 'INV-' . str_pad((string) ($index + 1001), 4, '0', STR_PAD_LEFT),
                'payment_method' => $index % 2 === 0 ? 'monnify' : 'card',
                'status' => $patient['status'] === 'confirmed' || $patient['status'] === 'completed' ? 'paid' : 'pending',
                'amount' => $patient['amount'],
                'meta' => ['gateway' => 'local', 'seeded' => true],
            ]);
        }
    }
}
