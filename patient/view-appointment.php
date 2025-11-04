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

// Get appointment details
$stmt = $db->prepare("
    SELECT a.*, d.name as doctor_name, d.specialization, d.phone as doctor_phone
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.id = ? AND a.patient_id = ?
");
$stmt->execute([$id, $patient['id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    redirect('/patient/appointments.php');
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $stmt = $db->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
    $stmt->execute([$id]);
    redirect('/patient/appointments.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Appointment Details</h1>
        
        <div class="card">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%;">Date:</td>
                    <td style="padding: 0.75rem;"><?php echo formatDate($appointment['appointment_date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Time:</td>
                    <td style="padding: 0.75rem;"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Doctor:</td>
                    <td style="padding: 0.75rem;">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Specialization:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Doctor Phone:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['doctor_phone']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Status:</td>
                    <td style="padding: 0.75rem;">
                        <span class="badge badge-<?php 
                            echo $appointment['status'] === 'Confirmed' ? 'success' : 
                                ($appointment['status'] === 'Completed' ? 'primary' :
                                ($appointment['status'] === 'Cancelled' ? 'danger' : 'warning')); 
                        ?>">
                            <?php echo $appointment['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php if ($appointment['notes']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Notes:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
            
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                <a href="/hms/patient/appointments.php" class="btn btn-secondary">Back to Appointments</a>
                <?php if ($appointment['status'] === 'Pending' || $appointment['status'] === 'Confirmed'): ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                    <button type="submit" name="cancel" class="btn btn-danger">Cancel Appointment</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
