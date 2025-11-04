<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'doctor') {
    redirect('/login.php');
}

$id = $_GET['id'] ?? 0;
$db = Database::getInstance()->getConnection();

// Get doctor info
$stmt = $db->prepare("SELECT * FROM doctors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$doctor = $stmt->fetch();

// Get patient details
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    redirect('/doctor/patients.php');
}

// Get patient's appointments with this doctor
$stmt = $db->prepare("
    SELECT * FROM appointments 
    WHERE patient_id = ? AND doctor_id = ?
    ORDER BY appointment_date DESC, appointment_time DESC
    LIMIT 10
");
$stmt->execute([$id, $doctor['id']]);
$appointments = $stmt->fetchAll();

// Get patient's medical records from this doctor
$stmt = $db->prepare("
    SELECT * FROM medical_records 
    WHERE patient_id = ? AND doctor_id = ?
    ORDER BY record_date DESC
    LIMIT 10
");
$stmt->execute([$id, $doctor['id']]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Patient Details</h1>
        
        <div class="card">
            <div class="card-header">Personal Information</div>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%;">Name:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($patient['name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">IC Number:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($patient['ic_number']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Phone:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($patient['phone']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Date of Birth:</td>
                    <td style="padding: 0.75rem;"><?php echo formatDate($patient['date_of_birth']); ?> 
                        (<?php echo floor((time() - strtotime($patient['date_of_birth'])) / (365 * 24 * 60 * 60)); ?> years old)
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Gender:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($patient['gender']); ?></td>
                </tr>
                <?php if ($patient['blood_type']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Blood Type:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($patient['blood_type']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($patient['address']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Address:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($patient['address'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="card">
            <div class="card-header">Recent Appointments</div>
            <?php if (count($appointments) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><?php echo formatDate($apt['appointment_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $apt['status'] === 'Confirmed' ? 'success' : 
                                        ($apt['status'] === 'Completed' ? 'primary' :
                                        ($apt['status'] === 'Cancelled' ? 'danger' : 'warning')); 
                                ?>">
                                    <?php echo $apt['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="view-appointment.php?id=<?php echo $apt['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No appointments found</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-header flex justify-between align-center">
                <span>Medical Records</span>
                <a href="add-record.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-success">Add New Record</a>
            </div>
            <?php if (count($records) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Diagnosis</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo formatDateTime($record['record_date']); ?></td>
                            <td><?php echo htmlspecialchars(substr($record['diagnosis'], 0, 60)) . (strlen($record['diagnosis']) > 60 ? '...' : ''); ?></td>
                            <td>
                                <a href="view-record.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No medical records found</p>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 1rem;">
            <a href="/hms/doctor/patients.php" class="btn btn-secondary">Back to Patients</a>
        </div>
    </div>
</body>
</html>
