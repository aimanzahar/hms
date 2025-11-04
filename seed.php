<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

try {
    // Check if data already exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        die("Database already has data. Seeding cancelled to prevent duplicates.\n");
    }
    
    echo "Starting database seeding with Malaysian context...\n\n";
    
    // Create doctors
    $doctors = [
        [
            'name' => 'Ahmad bin Abdullah',
            'email' => 'dr.ahmad@hms.my',
            'specialization' => 'General Practitioner',
            'phone' => '012-3456789',
            'license_number' => 'MMC-12345'
        ],
        [
            'name' => 'Siti Nurhaliza binti Hassan',
            'email' => 'dr.siti@hms.my',
            'specialization' => 'Pediatrician',
            'phone' => '013-9876543',
            'license_number' => 'MMC-12346'
        ],
        [
            'name' => 'Kumar a/l Subramaniam',
            'email' => 'dr.kumar@hms.my',
            'specialization' => 'Cardiologist',
            'phone' => '014-5678901',
            'license_number' => 'MMC-12347'
        ],
        [
            'name' => 'Tan Mei Ling',
            'email' => 'dr.tan@hms.my',
            'specialization' => 'Dermatologist',
            'phone' => '016-7890123',
            'license_number' => 'MMC-12348'
        ],
        [
            'name' => 'Wong Li Wei',
            'email' => 'dr.wong@hms.my',
            'specialization' => 'Orthopedic Surgeon',
            'phone' => '017-8901234',
            'license_number' => 'MMC-12349'
        ]
    ];
    
    $password = password_hash('password', PASSWORD_DEFAULT);
    
    foreach ($doctors as $doctor) {
        $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'doctor')");
        $stmt->execute([$doctor['email'], $password]);
        $userId = $db->lastInsertId();
        
        $stmt = $db->prepare("INSERT INTO doctors (user_id, name, specialization, phone, license_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $doctor['name'], $doctor['specialization'], $doctor['phone'], $doctor['license_number']]);
        
        echo "✓ Created doctor: Dr. " . $doctor['name'] . " (" . $doctor['specialization'] . ")\n";
    }
    
    echo "\n";
    
    // Create patients
    $patients = [
        [
            'name' => 'Sarah binti Tan Abdullah',
            'email' => 'sarah.tan@email.my',
            'ic_number' => '950123-10-5678',
            'phone' => '012-9876543',
            'address' => 'No. 45, Jalan Melati 3/2, Taman Suria, 47100 Puchong, Selangor',
            'date_of_birth' => '1995-01-23',
            'gender' => 'Female',
            'blood_type' => 'A+'
        ],
        [
            'name' => 'Muhammad Faisal bin Omar',
            'email' => 'faisal.omar@email.my',
            'ic_number' => '880515-14-3421',
            'phone' => '013-2345678',
            'address' => 'Blok B-12-05, Kondominium Seri Intan, Jalan Raja Laut, 50350 Kuala Lumpur',
            'date_of_birth' => '1988-05-15',
            'gender' => 'Male',
            'blood_type' => 'B+'
        ],
        [
            'name' => 'Lim Hui Ying',
            'email' => 'lim.huiying@email.my',
            'ic_number' => '920308-08-7654',
            'phone' => '016-5432109',
            'address' => 'No. 78, Jalan Ipoh, Taman Kepong, 52100 Kuala Lumpur',
            'date_of_birth' => '1992-03-08',
            'gender' => 'Female',
            'blood_type' => 'O+'
        ],
        [
            'name' => 'Raj Kumar a/l Muthu',
            'email' => 'raj.kumar@email.my',
            'ic_number' => '851220-10-1234',
            'phone' => '019-8765432',
            'address' => 'No. 12, Lorong Tun Ismail 5, Taman Sri Sentosa, 80100 Johor Bahru, Johor',
            'date_of_birth' => '1985-12-20',
            'gender' => 'Male',
            'blood_type' => 'AB+'
        ],
        [
            'name' => 'Nurul Izzah binti Zainuddin',
            'email' => 'nurul.izzah@email.my',
            'ic_number' => '990605-05-9876',
            'phone' => '011-3456789',
            'address' => 'No. 88, Jalan Tasik Permaisuri 2, Bandar Tun Razak, 56000 Kuala Lumpur',
            'date_of_birth' => '1999-06-05',
            'gender' => 'Female',
            'blood_type' => 'O-'
        ],
        [
            'name' => 'Chen Wei Liang',
            'email' => 'chen.wei@email.my',
            'ic_number' => '870918-07-2468',
            'phone' => '012-6789012',
            'address' => 'No. 34, Jalan Pinang 12, Taman Desa, 58100 Kuala Lumpur',
            'date_of_birth' => '1987-09-18',
            'gender' => 'Male',
            'blood_type' => 'A-'
        ],
        [
            'name' => 'Aisha Sofea binti Ismail',
            'email' => 'aisha.sofea@email.my',
            'ic_number' => '940412-03-5555',
            'phone' => '014-9012345',
            'address' => 'No. 22, Jalan SS 21/37, Damansara Utama, 47400 Petaling Jaya, Selangor',
            'date_of_birth' => '1994-04-12',
            'gender' => 'Female',
            'blood_type' => 'B-'
        ],
        [
            'name' => 'Mohd Hafiz bin Rahman',
            'email' => 'hafiz.rahman@email.my',
            'ic_number' => '910727-11-3333',
            'phone' => '017-2345678',
            'address' => 'No. 56, Jalan Taming Sari 8, Taman Melawati, 53100 Kuala Lumpur',
            'date_of_birth' => '1991-07-27',
            'gender' => 'Male',
            'blood_type' => 'O+'
        ]
    ];
    
    $patientIds = [];
    foreach ($patients as $patient) {
        $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'patient')");
        $stmt->execute([$patient['email'], $password]);
        $userId = $db->lastInsertId();
        
        $stmt = $db->prepare("INSERT INTO patients (user_id, name, ic_number, phone, address, date_of_birth, gender, blood_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $patient['name'], $patient['ic_number'], $patient['phone'], $patient['address'], $patient['date_of_birth'], $patient['gender'], $patient['blood_type']]);
        
        $patientIds[] = $db->lastInsertId();
        echo "✓ Created patient: " . $patient['name'] . "\n";
    }
    
    echo "\n";
    
    // Create appointments
    $appointments = [
        ['patient_idx' => 0, 'doctor_id' => 1, 'date' => date('Y-m-d', strtotime('+2 days')), 'time' => '09:00:00', 'status' => 'Confirmed', 'notes' => 'Annual checkup'],
        ['patient_idx' => 1, 'doctor_id' => 2, 'date' => date('Y-m-d', strtotime('+3 days')), 'time' => '10:30:00', 'status' => 'Pending', 'notes' => 'Child vaccination'],
        ['patient_idx' => 2, 'doctor_id' => 3, 'date' => date('Y-m-d', strtotime('+5 days')), 'time' => '14:00:00', 'status' => 'Confirmed', 'notes' => 'Follow-up for high blood pressure'],
        ['patient_idx' => 3, 'doctor_id' => 4, 'date' => date('Y-m-d', strtotime('+1 day')), 'time' => '11:00:00', 'status' => 'Confirmed', 'notes' => 'Skin rash consultation'],
        ['patient_idx' => 4, 'doctor_id' => 1, 'date' => date('Y-m-d'), 'time' => '15:00:00', 'status' => 'Confirmed', 'notes' => 'Fever and cough'],
        ['patient_idx' => 5, 'doctor_id' => 5, 'date' => date('Y-m-d', strtotime('-5 days')), 'time' => '10:00:00', 'status' => 'Completed', 'notes' => 'Knee pain'],
        ['patient_idx' => 6, 'doctor_id' => 2, 'date' => date('Y-m-d', strtotime('-3 days')), 'time' => '13:30:00', 'status' => 'Completed', 'notes' => 'Regular checkup'],
        ['patient_idx' => 7, 'doctor_id' => 1, 'date' => date('Y-m-d', strtotime('-1 day')), 'time' => '16:00:00', 'status' => 'Completed', 'notes' => 'Flu symptoms']
    ];
    
    foreach ($appointments as $apt) {
        $stmt = $db->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patientIds[$apt['patient_idx']], $apt['doctor_id'], $apt['date'], $apt['time'], $apt['status'], $apt['notes']]);
        echo "✓ Created appointment for patient " . ($apt['patient_idx'] + 1) . " with doctor " . $apt['doctor_id'] . "\n";
    }
    
    echo "\n";
    
    // Create medical records for completed appointments
    $records = [
        [
            'patient_idx' => 5,
            'doctor_id' => 5,
            'diagnosis' => 'Knee osteoarthritis - mild to moderate degeneration',
            'treatment' => 'Physiotherapy recommended, prescribed anti-inflammatory medication',
            'prescription' => 'Celecoxib 200mg - Take once daily after meals for 14 days\nGlucosamine Sulfate 500mg - Take 3 times daily',
            'notes' => 'Patient advised to avoid strenuous activities. Follow-up in 2 weeks.'
        ],
        [
            'patient_idx' => 6,
            'doctor_id' => 2,
            'diagnosis' => 'Normal health status - no significant findings',
            'treatment' => 'Continue healthy lifestyle and regular exercise',
            'prescription' => 'Multivitamin supplements - Take once daily',
            'notes' => 'All vital signs normal. Next checkup in 6 months.'
        ],
        [
            'patient_idx' => 7,
            'doctor_id' => 1,
            'diagnosis' => 'Upper respiratory tract infection (URTI) - viral origin',
            'treatment' => 'Rest and increased fluid intake. Symptomatic treatment prescribed.',
            'prescription' => 'Paracetamol 500mg - Take every 6 hours when needed\nChlorpheniramine 4mg - Take twice daily\nVitamin C 1000mg - Take once daily',
            'notes' => 'Patient should rest for 3-5 days. Return if symptoms worsen or fever persists.'
        ]
    ];
    
    foreach ($records as $record) {
        $stmt = $db->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, prescription, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patientIds[$record['patient_idx']], $record['doctor_id'], $record['diagnosis'], $record['treatment'], $record['prescription'], $record['notes']]);
        echo "✓ Created medical record for patient " . ($record['patient_idx'] + 1) . "\n";
    }
    
    echo "\n";
    
    // Create bills
    $bills = [
        ['patient_idx' => 5, 'amount' => 180.00, 'description' => 'Orthopedic consultation and X-ray', 'status' => 'Paid', 'paid' => true],
        ['patient_idx' => 6, 'amount' => 120.00, 'description' => 'Pediatric checkup and vaccination', 'status' => 'Paid', 'paid' => true],
        ['patient_idx' => 7, 'amount' => 85.00, 'description' => 'General consultation and medication', 'status' => 'Pending', 'paid' => false],
        ['patient_idx' => 3, 'amount' => 150.00, 'description' => 'Dermatology consultation', 'status' => 'Pending', 'paid' => false]
    ];
    
    foreach ($bills as $bill) {
        $paidDate = $bill['paid'] ? date('Y-m-d H:i:s', strtotime('-2 days')) : null;
        $stmt = $db->prepare("INSERT INTO bills (patient_id, amount, description, status, paid_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$patientIds[$bill['patient_idx']], $bill['amount'], $bill['description'], $bill['status'], $paidDate]);
        echo "✓ Created bill for patient " . ($bill['patient_idx'] + 1) . " - RM " . number_format($bill['amount'], 2) . "\n";
    }
    
    echo "\n";
    echo "===========================================\n";
    echo "Database seeding completed successfully!\n";
    echo "===========================================\n\n";
    echo "Demo Login Credentials:\n";
    echo "-----------------------\n";
    echo "Doctor:\n";
    echo "  Email: dr.ahmad@hms.my\n";
    echo "  Password: password\n\n";
    echo "Patient:\n";
    echo "  Email: sarah.tan@email.my\n";
    echo "  Password: password\n\n";
    echo "All users have the same password: password\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
