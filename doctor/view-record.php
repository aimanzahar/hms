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

// Get medical record
$stmt = $db->prepare("
    SELECT mr.*, p.name as patient_name, p.ic_number, p.date_of_birth, p.gender, p.blood_type
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.id
    WHERE mr.id = ? AND mr.doctor_id = ?
");
$stmt->execute([$id, $doctor['id']]);
$record = $stmt->fetch();

if (!$record) {
    redirect('/doctor/medical-records.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Medical Record Details</h1>
        
        <div class="card">
            <div class="card-header">Patient Information</div>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%;">Name:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($record['patient_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">IC Number:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($record['ic_number']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Date of Birth:</td>
                    <td style="padding: 0.75rem;"><?php echo formatDate($record['date_of_birth']); ?> 
                        (<?php echo floor((time() - strtotime($record['date_of_birth'])) / (365 * 24 * 60 * 60)); ?> years old)
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Gender:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($record['gender']); ?></td>
                </tr>
                <?php if ($record['blood_type']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Blood Type:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($record['blood_type']); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="card">
            <div class="card-header">Medical Record - <?php echo formatDateTime($record['record_date']); ?></div>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%; vertical-align: top;">Diagnosis:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></td>
                </tr>
                <?php if ($record['treatment']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Treatment:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($record['treatment'])); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($record['prescription']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Prescription:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($record['prescription'])); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($record['notes']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Additional Notes:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($record['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div style="margin-top: 1rem;">
            <a href="/hms/doctor/medical-records.php" class="btn btn-secondary">Back to Medical Records</a>
        </div>
    </div>
</body>
</html>
