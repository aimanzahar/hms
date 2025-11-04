<?php
require_once 'config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $name = $_POST['name'] ?? '';
    $ic_number = $_POST['ic_number'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            
            // Create user account
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'patient')");
            $stmt->execute([$email, $hashedPassword]);
            $userId = $db->lastInsertId();
            
            // Create patient profile
            $stmt = $db->prepare("INSERT INTO patients (user_id, name, ic_number, phone, address, date_of_birth, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $ic_number, $phone, $address, $date_of_birth, $gender]);
            
            $db->commit();
            $success = 'Registration successful! You can now login.';
        } catch (PDOException $e) {
            $db->rollBack();
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                $error = 'Email or IC number already exists';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Healthcare Management System</title>
    <link rel="stylesheet" href="/hms/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card" style="max-width: 600px;">
            <div class="login-header">
                <h1 class="login-title">Patient Registration</h1>
                <p class="login-subtitle">Create your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">IC Number (e.g., 901234-05-6789) *</label>
                    <input type="text" name="ic_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g., 012-3456789" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            
            <div class="mt-3 text-center">
                <p style="color: var(--text-light); font-size: 0.875rem;">Already have an account? <a href="/hms/login.php" style="color: var(--primary-color);">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
