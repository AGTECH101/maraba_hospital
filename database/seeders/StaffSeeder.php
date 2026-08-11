<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373814/profile-images/Y34eP43o2iFyN4BpLGKWhTnVbb6yM6LroY50Hlgl.jpg',
                'specializations' => ['histopathology', 'chemical-pathology'],
            ],
            [
                'name' => 'Dr. James Okafor',
                'email' => 'james.okafor@marabahospital.com',
                'phone' => '+2348034567890',
                'role' => 'owner',
                'specialty' => 'Consultant Haematologist',
                'bio' => 'Specialist in blood disorders and coagulation medicine.',
                'salary' => 450000,
                'availability' => ['start' => '08:00', 'end' => '17:00', 'days' => ['Mon','Tue','Wed','Thu','Fri']],
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373772/profile-images/9cnWDyhRbQXkrTLdFfXODrHqwMkgM08KseglaAKl.jpg',
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
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373724/profile-images/atUNOSEtfplDISC4kUgJAhNxqHcygYP0UiYpAPkE.jpg',
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
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373610/profile-images/UMHfHTTBdcA2VoOlVNfjkKqyiNh2QF8juGyQdtxd.jpg',
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
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373653/profile-images/m9gdOrmLojHOBwAsDIyf49fsIDaCVpulEbsYfY8i.jpg',
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
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373581/profile-images/9wGI9Kfp8OXOY6WlWqb36XSuI4ufmEzvgwCBzzwb.jpg',
                'specializations' => [],
            ],
            [
                'name' => 'Adam',
                'email' => 'adam@gmail.com',
                'phone' => '+2349013708170',
                'role' => 'doctor',
                'specialty' => null,
                'bio' => 'Hello, Testing 1,2... Sound Check over',
                'salary' => 2500000000,
                'availability' => null,
                'image' => 'https://res.cloudinary.com/dkmk76rfw/image/upload/v1784373526/profile-images/KKSCGq1rbwnl0mAvX97aQ4hJ2PVJPpbXJGhhMlKf.jpg',
                'specializations' => [],
            ],
        ];

        foreach ($staffData as $data) {
            $specializationSlugs = $data['specializations'];
            unset($data['specializations']);

            // Use updateOrCreate to avoid duplicates and preserve existing passwords.
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'image' => $data['image'] ?? null,
                    'is_approved' => true,
                ]
            );

            // Only set password for newly created users – existing passwords remain untouched.
            if ($user->wasRecentlyCreated) {
                $user->password = Hash::make('mbh_password1');
                $user->save();
            }

            // Create or update staff record (email is the unique identifier)
            $staff = StaffMember::updateOrCreate(
                ['email' => $data['email']],
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'specialty' => $data['specialty'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'salary' => $data['salary'] ?? 0,
                    'availability' => $data['availability'] ?? null,
                    'image' => $data['image'] ?? null,
                ]
            );

            // Sync specializations if any
            if (!empty($specializationSlugs)) {
                $specializationIds = Specialization::whereIn('slug', $specializationSlugs)->pluck('id');
                $staff->specializations()->sync($specializationIds);
            }
        }
    }
}