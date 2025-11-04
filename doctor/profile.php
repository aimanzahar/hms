<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'doctor') {
    redirect('/login.php');
}

$db = Database::getInstance()->getConnection();

// Get doctor info
$stmt = $db->prepare("
    SELECT d.*, u.email 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    WHERE d.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$doctor = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    
    try {
        $stmt = $db->prepare("
            UPDATE doctors 
            SET name = ?, phone = ?, specialization = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$name, $phone, $specialization, $_SESSION['user_id']]);
        $success = 'Profile updated successfully!';
        
        // Refresh data
        $stmt = $db->prepare("
            SELECT d.*, u.email 
            FROM doctors d 
            JOIN users u ON d.user_id = u.id 
            WHERE d.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $doctor = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Failed to update profile. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">My Profile</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">Professional Information</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email (Cannot be changed)</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($doctor['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">License Number (Cannot be changed)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($doctor['license_number']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
