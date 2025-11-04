<?php
require_once 'config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] === 'doctor') {
                redirect('/doctor/dashboard.php');
            } elseif ($user['role'] === 'patient') {
                redirect('/patient/dashboard.php');
            } elseif ($user['role'] === 'admin') {
                redirect('/admin/dashboard.php');
            }
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Healthcare Management System</title>
    <link rel="stylesheet" href="/hms/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">🏥 HMS</h1>
                <p class="login-subtitle">Healthcare Management System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <div class="mt-3 text-center">
                <p style="color: var(--text-light); font-size: 0.875rem;">Don't have an account? <a href="/hms/register.php" style="color: var(--primary-color);">Register as Patient</a></p>
            </div>
            
            <div class="mt-3">
                <p style="color: var(--text-light); font-size: 0.875rem; text-align: center;"><strong>Demo Accounts:</strong></p>
                <p style="font-size: 0.75rem; color: var(--text-light); text-align: center;">
                    Doctor: dr.ahmad@hms.my / password<br>
                    Patient: sarah.tan@email.my / password
                </p>
            </div>
        </div>
    </div>
</body>
</html>
