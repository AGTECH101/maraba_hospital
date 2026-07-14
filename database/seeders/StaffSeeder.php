<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffData = [
            [
                'name' => 'Dr. Ifeoma Nwachukwu',
                'email' => 'ifeoma.nwachukwu@marabahospital.com',
                'phone' => '+2348031112233',
                'role' => 'doctor',
                'specialty' => 'Director of Medical Laboratory Services / Consultant Pathologist',
                'bio' => 'Oversees all diagnostic operations and clinical governance across the laboratory.',
                'salary' => 550000,
                'availability' => ['start' => '08:00', 'end' => '16:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']],
                'specializations' => ['histopathology', 'chemical-pathology'],
            ],
            [
                'name' => 'Dr. James Okafor',
                'email' => 'james.okafor@marabahospital.com',
                'phone' => '+2348034567890',
                'role' => 'doctor',
                'specialty' => 'Consultant Haematologist',
                'bio' => 'Specialist in blood disorders and coagulation medicine.',
                'salary' => 450000,
                'availability' => ['start' => '08:00', 'end' => '17:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']],
                'specializations' => ['haematology', 'blood-transfusion-science'],
            ],
            [
                'name' => 'Dr. Amara Okoro',
                'email' => 'amara.okoro@marabahospital.com',
                'phone' => '+2347037894521',
                'role' => 'doctor',
                'specialty' => 'Consultant Medical Microbiologist',
                'bio' => 'Focused on infection diagnostics and antimicrobial resistance monitoring.',
                'salary' => 420000,
                'availability' => ['start' => '08:00', 'end' => '16:00', 'days' => ['Mon','Wed','Fri']],
                'specializations' => ['medical-microbiology', 'immunology-serology'],
            ],
            [
                'name' => 'Tunde Adebayo',
                'email' => 'tunde.adebayo@marabahospital.com',
                'phone' => '+2349012345678',
                'role' => 'technician',
                'specialty' => 'Chief Medical Laboratory Scientist',
                'bio' => 'MLSCN-registered scientist supervising sample processing and quality control.',
                'salary' => 320000,
                'availability' => ['start' => '07:00', 'end' => '15:00', 'days' => ['Mon','Tue','Wed','Thu','Fri','Sat']],
                'specializations' => ['chemical-pathology', 'haematology'],
            ],
            [
                'name' => 'Blessing Eze',
                'email' => 'blessing.eze@marabahospital.com',
                'phone' => '+2348123456789',
                'role' => 'technician',
                'specialty' => 'Medical Laboratory Scientist – Microbiology Unit',
                'bio' => 'Handles culture, sensitivity testing, and specimen analysis.',
                'salary' => 280000,
                'availability' => ['start' => '08:00', 'end' => '16:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']],
                'specializations' => ['medical-microbiology'],
            ],
            [
                'name' => 'Grace Adebayo',
                'email' => 'grace.adebayo@marabahospital.com',
                'phone' => '+2347034567890',
                'role' => 'technician',
                'specialty' => 'Phlebotomist',
                'bio' => 'Responsible for sample collection and patient preparation.',
                'salary' => 200000,
                'availability' => ['start' => '07:00', 'end' => '15:00', 'days' => ['Mon','Tue','Wed','Thu','Fri','Sat']],
                'specializations' => [],
            ],
        ];

        foreach ($staffData as $data) {
            $specializationSlugs = $data['specializations'];
            unset($data['specializations']);

            // Every staff member is a User first — this mirrors how
            // AdminDashboardController::storeStaff() creates accounts
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'image' => null,
                    'password' => 'StaffPass123!', // hashed automatically via the 'hashed' cast
                    'is_approved' => true, // seeded staff are pre-approved for immediate testing
                ]
            );

            $staff = StaffMember::create([
                ...$data,
                'user_id' => $user->id,
            ]);

            if (!empty($specializationSlugs)) {
                $specializationIds = Specialization::whereIn('slug', $specializationSlugs)->pluck('id');
                $staff->specializations()->attach($specializationIds);
            }
        }
    }
}