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

$patient_id = $_GET['patient_id'] ?? 0;
$appointment_id = $_GET['appointment_id'] ?? null;

// Get patient info
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

if (!$patient) {
    redirect('/doctor/patients.php');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $prescription = $_POST['prescription'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if ($diagnosis) {
        try {
            $stmt = $db->prepare("
                INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, treatment, prescription, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$patient_id, $doctor['id'], $appointment_id, $diagnosis, $treatment, $prescription, $notes]);
            
            // Update appointment status to Completed if linked
            if ($appointment_id) {
                $stmt = $db->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
                $stmt->execute([$appointment_id]);
            }
            
            $success = 'Medical record added successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to add medical record. Please try again.';
        }
    } else {
        $error = 'Please fill in the diagnosis';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Record - HMS</title>
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
        <h1 style="margin-bottom: 1.5rem;">Add Medical Record</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">Patient: <?php echo htmlspecialchars($patient['name']); ?></div>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Diagnosis *</label>
                    <textarea name="diagnosis" class="form-control" required placeholder="Enter diagnosis details"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Treatment</label>
                    <textarea name="treatment" class="form-control" placeholder="Enter treatment plan"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Prescription</label>
                    <textarea name="prescription" class="form-control" placeholder="Enter prescription details"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="notes" class="form-control" placeholder="Any additional notes"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-success">Save Medical Record</button>
                    <a href="view-patient.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
