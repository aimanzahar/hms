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

// Get all appointments
$stmt = $db->prepare("
    SELECT a.*, p.name as patient_name, p.phone, p.ic_number
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$doctor['id']]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">All Appointments</h1>
        
        <div class="card">
            <?php if (count($appointments) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>IC Number</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><?php echo formatDate($apt['appointment_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($apt['ic_number']); ?></td>
                            <td><?php echo htmlspecialchars($apt['phone']); ?></td>
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
    </div>
</body>
</html>
