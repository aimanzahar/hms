<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ms_MY');

        $patientProfiles = [
            [
                'name' => 'Nur Aisyah binti Zulkifli',
                'email' => 'nuraisyah.zulkifli@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1989-03-12',
                'street' => 'Jalan Ampang',
                'postcode' => '50450',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'medical_history' => 'Type 2 Diabetes Mellitus',
            ],
            [
                'name' => 'Muhammad Arif bin Hassan',
                'email' => 'muhammadarif.hassan@yahoo.com.my',
                'gender' => 'male',
                'date_of_birth' => '1978-11-02',
                'street' => 'Lebuh Pantai',
                'postcode' => '10300',
                'city' => 'George Town',
                'state' => 'Pulau Pinang',
                'medical_history' => 'Hypertension',
            ],
            [
                'name' => 'Lim Xiao Ying',
                'email' => 'lim.xiaoying@hotmail.com',
                'gender' => 'female',
                'date_of_birth' => '1992-07-21',
                'street' => 'Jalan Wong Ah Fook',
                'postcode' => '80000',
                'city' => 'Johor Bahru',
                'state' => 'Johor',
                'medical_history' => null,
            ],
            [
                'name' => 'Rajesh a/l Kumar',
                'email' => 'rajesh.kumar@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1984-05-17',
                'street' => 'Jalan Sultan Idris Shah',
                'postcode' => '30000',
                'city' => 'Ipoh',
                'state' => 'Perak',
                'medical_history' => 'Asthma',
            ],
            [
                'name' => 'Siti Khadijah binti Abdul Rahman',
                'email' => 'sitikhadijah.rahman@yahoo.com.my',
                'gender' => 'female',
                'date_of_birth' => '1965-01-08',
                'street' => 'Jalan Tunku Abdul Rahman',
                'postcode' => '93100',
                'city' => 'Kuching',
                'state' => 'Sarawak',
                'medical_history' => 'Hypertension',
            ],
            [
                'name' => 'Chong Wei Han',
                'email' => 'chong.weihan@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1972-09-29',
                'street' => 'Jalan Gaya',
                'postcode' => '88000',
                'city' => 'Kota Kinabalu',
                'state' => 'Sabah',
                'medical_history' => 'Gastritis',
            ],
            [
                'name' => 'Hanim binti Mohd Salleh',
                'email' => 'hanim.salleh@hotmail.com',
                'gender' => 'female',
                'date_of_birth' => '1995-12-03',
                'street' => 'Persiaran Sultan',
                'postcode' => '40000',
                'city' => 'Shah Alam',
                'state' => 'Selangor',
                'medical_history' => null,
            ],
            [
                'name' => 'Ganesh a/l Subramaniam',
                'email' => 'ganesh.subramaniam@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1988-04-14',
                'street' => 'Jalan Universiti',
                'postcode' => '46200',
                'city' => 'Petaling Jaya',
                'state' => 'Selangor',
                'medical_history' => 'Lower Back Pain',
            ],
            [
                'name' => 'Cheong Mei Siew',
                'email' => 'cheong.meisiew@yahoo.com.my',
                'gender' => 'female',
                'date_of_birth' => '1970-02-25',
                'street' => 'Jalan Hang Jebat',
                'postcode' => '75200',
                'city' => 'Melaka',
                'state' => 'Melaka',
                'medical_history' => 'Type 2 Diabetes Mellitus',
            ],
            [
                'name' => 'Mohd Hafiz bin Mat Isa',
                'email' => 'hafiz.matisa@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1999-08-19',
                'street' => "Jalan Dato' Bandar Tunggal",
                'postcode' => '70300',
                'city' => 'Seremban',
                'state' => 'Negeri Sembilan',
                'medical_history' => null,
            ],
            [
                'name' => 'Nurul Amirah binti Saad',
                'email' => 'nurul.amirahsaad@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1990-06-09',
                'street' => 'Jalan Sultanah',
                'postcode' => '05000',
                'city' => 'Alor Setar',
                'state' => 'Kedah',
                'medical_history' => 'Migraine',
            ],
            [
                'name' => 'Peter Anak Joseph',
                'email' => 'peter.joseph@hotmail.com',
                'gender' => 'male',
                'date_of_birth' => '1958-12-30',
                'street' => 'Jalan Merpati',
                'postcode' => '98000',
                'city' => 'Miri',
                'state' => 'Sarawak',
                'medical_history' => 'Hypertension',
            ],
            [
                'name' => 'Tan Kok Leong',
                'email' => 'tan.kokleong@yahoo.com',
                'gender' => 'male',
                'date_of_birth' => '1976-04-06',
                'street' => 'Jalan Rahmat',
                'postcode' => '83000',
                'city' => 'Batu Pahat',
                'state' => 'Johor',
                'medical_history' => 'Hyperlipidemia',
            ],
            [
                'name' => 'Faridah binti Omar',
                'email' => 'faridah.omar@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1982-03-28',
                'street' => 'Jalan Beserah',
                'postcode' => '26100',
                'city' => 'Kuantan',
                'state' => 'Pahang',
                'medical_history' => 'Asthma',
            ],
            [
                'name' => 'Lee Soon Hui',
                'email' => 'lee.soonhui@hotmail.com',
                'gender' => 'female',
                'date_of_birth' => '1969-10-11',
                'street' => 'Jalan Kota',
                'postcode' => '34000',
                'city' => 'Taiping',
                'state' => 'Perak',
                'medical_history' => null,
            ],
            [
                'name' => 'Ahmad Nizam bin Yusof',
                'email' => 'ahmadnizam.yusof@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1996-01-23',
                'street' => 'Jalan Sultan Zainal Abidin',
                'postcode' => '20000',
                'city' => 'Kuala Terengganu',
                'state' => 'Terengganu',
                'medical_history' => 'Allergic rhinitis',
            ],
            [
                'name' => 'Thivya a/p Manimaran',
                'email' => 'thivya.manimaran@yahoo.com.my',
                'gender' => 'female',
                'date_of_birth' => '2001-09-05',
                'street' => 'Taman Ria Jaya',
                'postcode' => '08000',
                'city' => 'Sungai Petani',
                'state' => 'Kedah',
                'medical_history' => null,
            ],
            [
                'name' => 'Muhammad Danial bin Rosli',
                'email' => 'danial.rosli@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1994-11-15',
                'street' => 'Persiaran Perdana',
                'postcode' => '62000',
                'city' => 'Putrajaya',
                'state' => 'Wilayah Persekutuan Putrajaya',
                'medical_history' => 'Gastritis',
            ],
            [
                'name' => 'Liew Jia Qi',
                'email' => 'liew.jiaqi@hotmail.com',
                'gender' => 'female',
                'date_of_birth' => '1987-02-07',
                'street' => 'Jalan BBN 1/1',
                'postcode' => '71800',
                'city' => 'Nilai',
                'state' => 'Negeri Sembilan',
                'medical_history' => null,
            ],
            [
                'name' => 'Roslan bin Ibrahim',
                'email' => 'roslan.ibrahim@yahoo.com',
                'gender' => 'male',
                'date_of_birth' => '1960-05-02',
                'street' => 'Jalan Leila',
                'postcode' => '90703',
                'city' => 'Sandakan',
                'state' => 'Sabah',
                'medical_history' => 'Hypertension',
            ],
            [
                'name' => 'Agnes Anak Ling',
                'email' => 'agnes.ling@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1974-07-16',
                'street' => 'Jalan Kidurong',
                'postcode' => '97000',
                'city' => 'Bintulu',
                'state' => 'Sarawak',
                'medical_history' => 'Type 2 Diabetes Mellitus',
            ],
            [
                'name' => 'Sharifah Nadia binti Syed Azmi',
                'email' => 'sharifah.nadia@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1993-09-27',
                'street' => 'Jalan Meru',
                'postcode' => '41050',
                'city' => 'Klang',
                'state' => 'Selangor',
                'medical_history' => null,
            ],
            [
                'name' => 'Kumaravel a/l Selvaraj',
                'email' => 'kumaravel.selvaraj@hotmail.com',
                'gender' => 'male',
                'date_of_birth' => '1981-08-01',
                'street' => 'Jalan Teknokrat',
                'postcode' => '63000',
                'city' => 'Cyberjaya',
                'state' => 'Selangor',
                'medical_history' => 'Lower Back Pain',
            ],
            [
                'name' => 'Azimah binti Rahmat',
                'email' => 'azimah.rahmat@yahoo.com',
                'gender' => 'female',
                'date_of_birth' => '1950-04-04',
                'street' => 'Jalan Bahagia',
                'postcode' => '28000',
                'city' => 'Temerloh',
                'state' => 'Pahang',
                'medical_history' => 'Hypertension',
            ],
            [
                'name' => 'Lim Wei Jie',
                'email' => 'lim.weijie@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '2003-12-19',
                'street' => 'Jalan Tun Mustapha',
                'postcode' => '87000',
                'city' => 'Labuan',
                'state' => 'Wilayah Persekutuan Labuan',
                'medical_history' => null,
            ],
        ];

        $prefixes = ['010', '011', '012', '013'];

        DB::transaction(function () use ($patientProfiles, $faker, $prefixes) {
            foreach ($patientProfiles as $profile) {
                $phone = $this->generatePhoneNumber($faker, $prefixes);
                $emergencyContact = $this->generatePhoneNumber($faker, $prefixes, $phone);

                $user = User::updateOrCreate(
                    ['email' => $profile['email']],
                    [
                        'name' => $profile['name'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'role' => 'patient',
                    ]
                );

                $address = sprintf(
                    '%s, %s %s, %s',
                    $profile['street'],
                    $profile['postcode'],
                    $profile['city'],
                    $profile['state']
                );

                Patient::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'date_of_birth' => $profile['date_of_birth'],
                        'gender' => $profile['gender'],
                        'phone' => $phone,
                        'address' => $address,
                        'emergency_contact' => $emergencyContact,
                        'medical_history' => $profile['medical_history'],
                    ]
                );
            }
        });
    }

    private function generatePhoneNumber(Generator $faker, array $prefixes, ?string $exclude = null): string
    {
        do {
            $prefix = $prefixes[array_rand($prefixes)];
            $pattern = $prefix === '011' ? '####-####' : '###-####';
            $number = $prefix . '-' . $faker->numerify($pattern);
        } while ($number === $exclude);

        return $number;
    }
}