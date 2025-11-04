<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctorProfiles = [
            [
                'name' => 'Dr. Ahmad bin Abdullah',
                'email' => 'ahmad.abdullah@gmail.com',
                'specialization' => 'Pakar Perubatan Am',
                'license_number' => 'MMC-12001',
                'experience_years' => 18,
                'consultation_fee' => 120.00,
            ],
            [
                'name' => 'Dr. Siti Nurhaliza binti Mohd Ali',
                'email' => 'sitinurhaliza.ali@yahoo.com.my',
                'specialization' => 'Pakar Sakit Puan',
                'license_number' => 'MMC-12002',
                'experience_years' => 22,
                'consultation_fee' => 180.00,
            ],
            [
                'name' => 'Dr. Muhammad Firdaus bin Ismail',
                'email' => 'firdaus.ismail@hotmail.com',
                'specialization' => 'Pakar Jantung',
                'license_number' => 'MMC-12003',
                'experience_years' => 15,
                'consultation_fee' => 250.00,
            ],
            [
                'name' => 'Dr. Tan Wei Ming',
                'email' => 'tan.weiming@gmail.com',
                'specialization' => 'Pakar Bedah',
                'license_number' => 'MMC-12004',
                'experience_years' => 12,
                'consultation_fee' => 220.00,
            ],
            [
                'name' => 'Dr. Lee Mei Ling',
                'email' => 'lee.meiling@yahoo.com.my',
                'specialization' => 'Pakar Kanak-kanak',
                'license_number' => 'MMC-12005',
                'experience_years' => 10,
                'consultation_fee' => 160.00,
            ],
            [
                'name' => 'Dr. Lim Jia Hui',
                'email' => 'lim.jiahui@gmail.com',
                'specialization' => 'Pakar Kulit',
                'license_number' => 'MMC-12006',
                'experience_years' => 9,
                'consultation_fee' => 190.00,
            ],
            [
                'name' => 'Dr. Raj Kumar a/l Muthusamy',
                'email' => 'raj.kumar@hotmail.com',
                'specialization' => 'Pakar Ortopedik',
                'license_number' => 'MMC-12007',
                'experience_years' => 20,
                'consultation_fee' => 230.00,
            ],
            [
                'name' => 'Dr. Priya Devi a/p Subramaniam',
                'email' => 'priya.devi@yahoo.com.my',
                'specialization' => 'Pakar Sakit Puan',
                'license_number' => 'MMC-12008',
                'experience_years' => 14,
                'consultation_fee' => 210.00,
            ],
            [
                'name' => 'Dr. Wong Chee Seng',
                'email' => 'wong.cheeseng@gmail.com',
                'specialization' => 'Pakar Mata',
                'license_number' => 'MMC-12009',
                'experience_years' => 11,
                'consultation_fee' => 170.00,
            ],
            [
                'name' => 'Dr. Nurul Aini binti Hassan',
                'email' => 'nurulaini.hassan@hotmail.com',
                'specialization' => 'Pakar Perubatan Am',
                'license_number' => 'MMC-12010',
                'experience_years' => 8,
                'consultation_fee' => 130.00,
            ],
            [
                'name' => 'Dr. Chandra Bala a/l Rajan',
                'email' => 'chandra.bala@gmail.com',
                'specialization' => 'Pakar Jantung',
                'license_number' => 'MMC-12011',
                'experience_years' => 26,
                'consultation_fee' => 300.00,
            ],
            [
                'name' => 'Dr. Farah Hanis binti Rahman',
                'email' => 'farahhanis.rahman@yahoo.com.my',
                'specialization' => 'Pakar Kanak-kanak',
                'license_number' => 'MMC-12012',
                'experience_years' => 7,
                'consultation_fee' => 150.00,
            ],
            [
                'name' => 'Dr. Goh Jia Hao',
                'email' => 'goh.jiahao@gmail.com',
                'specialization' => 'Pakar Ortopedik',
                'license_number' => 'MMC-12013',
                'experience_years' => 5,
                'consultation_fee' => 200.00,
            ],
            [
                'name' => 'Dr. Kavitha Rani a/p Manogaran',
                'email' => 'kavitharani.mano@hotmail.com',
                'specialization' => 'Pakar Mata',
                'license_number' => 'MMC-12014',
                'experience_years' => 17,
                'consultation_fee' => 240.00,
            ],
        ];

        DB::transaction(function () use ($doctorProfiles) {
            foreach ($doctorProfiles as $profile) {
                $user = User::updateOrCreate(
                    ['email' => $profile['email']],
                    [
                        'name' => $profile['name'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'role' => 'doctor',
                    ]
                );

                Doctor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'specialization' => $profile['specialization'],
                        'license_number' => $profile['license_number'],
                        'experience_years' => $profile['experience_years'],
                        'consultation_fee' => $profile['consultation_fee'],
                    ]
                );
            }
        });
    }
}