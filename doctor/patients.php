<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'doctor') {
    redirect('/login.php');
}

$db = Database::getInstance()->getConnection();

// Get doctor info
$stmt = $db->prepare("SELECT * FROM doctors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$doctor = $stmt->fetch();

// Get all patients who have appointments with this doctor
$stmt = $db->prepare("
    SELECT DISTINCT p.*, 
           (SELECT COUNT(*) FROM appointments WHERE patient_id = p.id AND doctor_id = ?) as total_appointments,
           (SELECT COUNT(*) FROM medical_records WHERE patient_id = p.id AND doctor_id = ?) as total_records
    FROM patients p
    JOIN appointments a ON p.id = a.patient_id
    WHERE a.doctor_id = ?
    ORDER BY p.name
");
$stmt->execute([$doctor['id'], $doctor['id'], $doctor['id']]);
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - HMS</title>
    <link rel="stylesheet" href="/hms/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/hms/doctor/dashboard.php" class="navbar-brand">🏥 HMS - Doctor</a>
            <div class="navbar-menu">
                <a href="/hms/doctor/dashboard.php">Dashboard</a>
                <a href="/hms/doctor/appointments.php">Appointments</a>
                <a href="/hms/doctor/patients.php">Patients</a>
                <a href="/hms/doctor/medical-records.php">Medical Records</a>
                <a href="/hms/doctor/profile.php">Profile</a>
                <a href="/hms/logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1 style="margin-bottom: 1.5rem;">My Patients</h1>
        
        <div class="card">
            <?php if (count($patients) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>IC Number</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Blood Type</th>
                            <th>Appointments</th>
                            <th>Records</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['ic_number']); ?></td>
                            <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                            <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                            <td><?php echo htmlspecialchars($patient['blood_type'] ?: '-'); ?></td>
                            <td><?php echo $patient['total_appointments']; ?></td>
                            <td><?php echo $patient['total_records']; ?></td>
                            <td>
                                <a href="view-patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No patients found</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
