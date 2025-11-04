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

// Get statistics
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM appointments 
    WHERE doctor_id = ? AND appointment_date >= date('now')
");
$stmt->execute([$doctor['id']]);
$upcomingAppointments = $stmt->fetchColumn();

$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM appointments 
    WHERE doctor_id = ? AND appointment_date = date('now')
");
$stmt->execute([$doctor['id']]);
$todayAppointments = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) as total FROM medical_records WHERE doctor_id = ?");
$stmt->execute([$doctor['id']]);
$totalRecords = $stmt->fetchColumn();

// Get today's appointments
$stmt = $db->prepare("
    SELECT a.*, p.name as patient_name, p.phone, p.ic_number
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ? AND a.appointment_date = date('now')
    ORDER BY a.appointment_time
");
$stmt->execute([$doctor['id']]);
$todaysList = $stmt->fetchAll();

// Get upcoming appointments
$stmt = $db->prepare("
    SELECT a.*, p.name as patient_name, p.phone
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ? AND a.appointment_date > date('now')
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5
");
$stmt->execute([$doctor['id']]);
$upcomingList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Welcome, Dr. <?php echo htmlspecialchars($doctor['name']); ?></h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Today's Appointments</div>
                <div class="stat-value"><?php echo $todayAppointments; ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-label">Upcoming Appointments</div>
                <div class="stat-value"><?php echo $upcomingAppointments; ?></div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-label">Total Medical Records</div>
                <div class="stat-value"><?php echo $totalRecords; ?></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Today's Appointments</div>
            <?php if (count($todaysList) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>IC Number</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todaysList as $apt): ?>
                        <tr>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($apt['ic_number']); ?></td>
                            <td><?php echo htmlspecialchars($apt['phone']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $apt['status'] === 'Confirmed' ? 'success' : 
                                        ($apt['status'] === 'Pending' ? 'warning' : 'primary'); 
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
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No appointments for today</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-header">Upcoming Appointments</div>
            <?php if (count($upcomingList) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingList as $apt): ?>
                        <tr>
                            <td><?php echo formatDate($apt['appointment_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($apt['phone']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $apt['status'] === 'Confirmed' ? 'success' : 
                                        ($apt['status'] === 'Pending' ? 'warning' : 'primary'); 
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
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No upcoming appointments</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
