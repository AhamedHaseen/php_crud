<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle password fix request
if (isset($_GET['fix_password']) && $_GET['fix_password'] == '1') {
    try {
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
        $result = $stmt->execute([$newHash]);
        
        if ($result) {
            $success = 'Password hash updated successfully! You can now login with admin/admin123';
        } else {
            $error = 'Failed to update password hash';
        }
    } catch (Exception $e) {
        $error = 'Error updating password: ' . $e->getMessage();
    }
}

if ($_POST) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Debug: Log the login attempt
    error_log("Login attempt - Username: " . $username . ", Password: " . $password);
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // Debug: Check if admin user exists
    if (!$admin) {
        $error = 'Username not found in database';
        error_log("Login failed - Username not found: " . $username);
    } else {
        // Debug: Log the stored hash
        error_log("Stored password hash: " . $admin['password']);
        error_log("Attempting to verify password: " . $password);
        
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            error_log("Login successful for: " . $username);
            redirect('dashboard.php');
        } else {
            $error = 'Password verification failed';
            error_log("Password verification failed for: " . $username);
            
            // Additional debug: Try manual verification
            $manualHash = password_hash($password, PASSWORD_DEFAULT);
            error_log("Generated hash for comparison: " . $manualHash);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CRUD Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            border: none;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
            color: white;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h3>Admin Login</h3>
                        <p class="mb-0">CRUD Dashboard</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                
                                <!-- Debug Information -->
                                <?php if (isset($admin) && $admin): ?>
                                <hr>
                                <small><strong>Debug Info:</strong></small><br>
                                <small>Username found: ✅</small><br>
                                <small>Password Hash: <?php echo substr($admin['password'], 0, 20) . '...'; ?></small><br>
                                <small>Hash Length: <?php echo strlen($admin['password']); ?> characters</small><br>
                                <small>PHP Version: <?php echo phpversion(); ?></small><br>
                                
                                <!-- Instant Fix Button -->
                                <div class="mt-3">
                                    <a href="?fix_password=1" class="btn btn-sm btn-warning">
                                        <i class="fas fa-wrench"></i> Fix Password Hash Now
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                Default credentials: <strong>admin</strong> / <strong>admin123</strong>
                            </small><br>
                            <div class="mt-2">
                                <a href="debug_login.php" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-tools"></i> Fix Login Issues
                                </a>
                                <a href="reset_admin.php" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-key"></i> Reset Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
