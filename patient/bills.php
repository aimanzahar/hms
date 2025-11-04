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

// Get bills
$stmt = $db->prepare("
    SELECT b.*, 
           CASE WHEN b.appointment_id IS NOT NULL 
                THEN (SELECT d.name FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.id = b.appointment_id)
                ELSE 'N/A'
           END as doctor_name
    FROM bills b
    WHERE b.patient_id = ?
    ORDER BY b.bill_date DESC
");
$stmt->execute([$patient['id']]);
$bills = $stmt->fetchAll();

// Calculate totals
$totalPending = 0;
$totalPaid = 0;
foreach ($bills as $bill) {
    if ($bill['status'] === 'Pending') {
        $totalPending += $bill['amount'];
    } elseif ($bill['status'] === 'Paid') {
        $totalPaid += $bill['amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">My Bills</h1>
        
        <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="stat-card warning">
                <div class="stat-label">Pending Amount</div>
                <div class="stat-value"><?php echo formatCurrency($totalPending); ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-label">Total Paid</div>
                <div class="stat-value"><?php echo formatCurrency($totalPaid); ?></div>
            </div>
        </div>
        
        <div class="card">
            <?php if (count($bills) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bill Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bills as $bill): ?>
                        <tr>
                            <td><?php echo formatDate($bill['bill_date']); ?></td>
                            <td><?php echo htmlspecialchars($bill['description']); ?></td>
                            <td><strong><?php echo formatCurrency($bill['amount']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $bill['status'] === 'Paid' ? 'success' : 
                                        ($bill['status'] === 'Cancelled' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo $bill['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $bill['paid_date'] ? formatDate($bill['paid_date']) : '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-light); text-align: center; padding: 2rem;">No bills found</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
