<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'patient') {
    redirect('/login.php');
}

$db = Database::getInstance()->getConnection();

// Get patient info
$stmt = $db->prepare("SELECT * FROM patients WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch();

// Get all appointments
$stmt = $db->prepare("
    SELECT a.*, d.name as doctor_name, d.specialization, d.phone as doctor_phone
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$patient['id']]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - HMS</title>
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
        <div class="flex justify-between align-center mb-3">
            <h1>My Appointments</h1>
            <a href="/hms/patient/book-appointment.php" class="btn btn-primary">Book New Appointment</a>
        </div>
        
        <div class="card">
            <?php if (count($appointments) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><?php echo formatDate($apt['appointment_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($apt['specialization']); ?></td>
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
