<?php
require_once '../config/config.php';
requireLogin();

if (getUserRole() !== 'patient') {
    redirect('/login.php');
}

$db = Database::getInstance()->getConnection();

// Get patient info
$stmt = $db->prepare("
    SELECT p.*, u.email 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $blood_type = $_POST['blood_type'] ?? '';
    
    try {
        $stmt = $db->prepare("
            UPDATE patients 
            SET name = ?, phone = ?, address = ?, blood_type = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$name, $phone, $address, $blood_type, $_SESSION['user_id']]);
        $success = 'Profile updated successfully!';
        
        // Refresh data
        $stmt = $db->prepare("
            SELECT p.*, u.email 
            FROM patients p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $patient = $stmt->fetch();
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
        <h1 style="margin-bottom: 1.5rem;">My Profile</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">Personal Information</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($patient['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email (Cannot be changed)</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($patient['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">IC Number (Cannot be changed)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['ic_number']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date of Birth (Cannot be changed)</label>
                    <input type="date" class="form-control" value="<?php echo $patient['date_of_birth']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Gender (Cannot be changed)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['gender']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Blood Type</label>
                    <select name="blood_type" class="form-control">
                        <option value="">Select Blood Type</option>
                        <?php 
                        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                        foreach ($bloodTypes as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo $patient['blood_type'] === $type ? 'selected' : ''; ?>>
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
