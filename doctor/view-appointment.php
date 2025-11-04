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

// Get appointment details
$stmt = $db->prepare("
    SELECT a.*, p.name as patient_name, p.phone, p.ic_number, p.date_of_birth, p.gender, p.blood_type, p.address
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->execute([$id, $doctor['id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    redirect('/doctor/appointments.php');
}

$success = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        $success = 'Appointment status updated successfully!';
        
        // Refresh appointment data
        $stmt = $db->prepare("
            SELECT a.*, p.name as patient_name, p.phone, p.ic_number, p.date_of_birth, p.gender, p.blood_type, p.address
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            WHERE a.id = ? AND a.doctor_id = ?
        ");
        $stmt->execute([$id, $doctor['id']]);
        $appointment = $stmt->fetch();
    }
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
        <h1 style="margin-bottom: 1.5rem;">Appointment Details</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">Appointment Information</div>
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
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Patient Notes:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="card">
            <div class="card-header">Patient Information</div>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; width: 30%;">Name:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">IC Number:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['ic_number']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Phone:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['phone']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Date of Birth:</td>
                    <td style="padding: 0.75rem;"><?php echo formatDate($appointment['date_of_birth']); ?> 
                        (<?php echo floor((time() - strtotime($appointment['date_of_birth'])) / (365 * 24 * 60 * 60)); ?> years old)
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Gender:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['gender']); ?></td>
                </tr>
                <?php if ($appointment['blood_type']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600;">Blood Type:</td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($appointment['blood_type']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($appointment['address']): ?>
                <tr>
                    <td style="padding: 0.75rem; font-weight: 600; vertical-align: top;">Address:</td>
                    <td style="padding: 0.75rem;"><?php echo nl2br(htmlspecialchars($appointment['address'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php if ($appointment['status'] !== 'Cancelled' && $appointment['status'] !== 'Completed'): ?>
        <div class="card">
            <div class="card-header">Update Appointment Status</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="Pending" <?php echo $appointment['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Confirmed" <?php echo $appointment['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="Completed" <?php echo $appointment['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $appointment['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <a href="/hms/doctor/appointments.php" class="btn btn-secondary">Back to Appointments</a>
            <?php if ($appointment['status'] === 'Confirmed' || $appointment['status'] === 'Completed'): ?>
            <a href="add-record.php?patient_id=<?php echo $appointment['patient_id']; ?>&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-success">Add Medical Record</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
