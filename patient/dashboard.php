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

// Get statistics
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM appointments 
    WHERE patient_id = ? AND appointment_date >= date('now')
");
$stmt->execute([$patient['id']]);
$upcomingAppointments = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) as total FROM medical_records WHERE patient_id = ?");
$stmt->execute([$patient['id']]);
$totalRecords = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM bills WHERE patient_id = ? AND status = 'Pending'");
$stmt->execute([$patient['id']]);
$pendingBills = $stmt->fetchColumn();

// Get upcoming appointments
$stmt = $db->prepare("
    SELECT a.*, d.name as doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ? AND a.appointment_date >= date('now')
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5
");
$stmt->execute([$patient['id']]);
$upcomingList = $stmt->fetchAll();

// Get recent medical records
$stmt = $db->prepare("
    SELECT mr.*, d.name as doctor_name, d.specialization
    FROM medical_records mr
    JOIN doctors d ON mr.doctor_id = d.id
    WHERE mr.patient_id = ?
    ORDER BY mr.record_date DESC
    LIMIT 5
");
$stmt->execute([$patient['id']]);
$recentRecords = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Welcome, <?php echo htmlspecialchars($patient['name']); ?></h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Upcoming Appointments</div>
                <div class="stat-value"><?php echo $upcomingAppointments; ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-label">Medical Records</div>
                <div class="stat-value"><?php echo $totalRecords; ?></div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-label">Pending Bills</div>
                <div class="stat-value"><?php echo formatCurrency($pendingBills); ?></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header flex justify-between align-center">
                <span>Upcoming Appointments</span>
                <a href="/hms/patient/book-appointment.php" class="btn btn-sm btn-primary">Book New Appointment</a>
            </div>
            <?php if (count($upcomingList) > 0): ?>
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
                        <?php foreach ($upcomingList as $apt): ?>
                        <tr>
                            <td><?php echo formatDate($apt['appointment_date']); ?></td>
                            <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($apt['specialization']); ?></td>
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
        
        <div class="card">
            <div class="card-header">Recent Medical Records</div>
            <?php if (count($recentRecords) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Diagnosis</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRecords as $record): ?>
                        <tr>
                            <td><?php echo formatDateTime($record['record_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($record['diagnosis'], 0, 50)) . (strlen($record['diagnosis']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <a href="view-record.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No medical records yet</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
