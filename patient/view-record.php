<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'patient') {
    redirect('/login.php');
}

$id = $_GET['id'] ?? 0;
$db = Database::getInstance()->getConnection();

// Get patient info
$stmt = $db->prepare("SELECT * FROM patients WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch();

// Get medical record
$stmt = $db->prepare("
    SELECT mr.*, d.name as doctor_name, d.specialization
    FROM medical_records mr
    JOIN doctors d ON mr.doctor_id = d.id
    WHERE mr.id = ? AND mr.patient_id = ?
");
$stmt->execute([$id, $patient['id']]);
$record = $stmt->fetch();

if (!$record) {
    redirect('/patient/medical-records.php');
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
            <a href="/hms/patient/dashboard.php" class="navbar-brand">🏥 HMS - Patient</a>
            <div class="navbar-menu">
                <a href="/hms/patient/dashboard.php">Dashboard</a>
                <a href="/hms/patient/appointments.php">My Appointments</a>
                <a href="/hms/patient/medical-records.php">Medical Records</a>
                <a href="/hms/patient/bills.php">Bills</a>
                <a href="/hms/patient/profile.php">Profile</a>
                <a href="/hms/logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1 style="margin-bottom: 1.5rem;">Medical Record Details</h1>
        
        <div class="card">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%;">Record Date:</td>
                    <td style="padding: 0.75rem;"><?php echo formatDateTime($record['record_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Doctor:</td>
                    <td style="padding: 0.75rem;">Dr. <?php echo htmlspecialchars($record['doctor_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Specialization:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($record['specialization']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Diagnosis:</td>
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
            
            <div style="margin-top: 1.5rem;">
                <a href="/hms/patient/medical-records.php" class="btn btn-secondary">Back to Medical Records</a>
            </div>
        </div>
    </div>
</body>
</html>
