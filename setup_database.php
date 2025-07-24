<?php
// Alternative Database Setup Script
// Run this if you're having issues with the admin login

require_once 'config.php';

echo "<h1>Database Setup & Admin Account Fixer</h1>";

try {
    // First, let's check if the database and tables exist
    echo "<h2>1. Checking Database Structure...</h2>";
    
    // Check if admins table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Admins table exists</p>";
    } else {
        echo "<p>❌ Admins table missing. Creating...</p>";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "<p>✅ Admins table created</p>";
    }
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Users table exists</p>";
    } else {
        echo "<p>❌ Users table missing. Creating...</p>";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                phone VARCHAR(15),
                age INT(3),
                address TEXT,
                status ENUM('active', 'inactive') DEFAULT 'active',
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "<p>✅ Users table created</p>";
    }
    
    echo "<h2>2. Fixing Admin Account...</h2>";
    
    // Delete existing admin if any
    $pdo->exec("DELETE FROM admins WHERE username = 'admin'");
    echo "<p>🗑️ Cleared existing admin account</p>";
    
    // Create new admin with correct password
    $username = 'admin';
    $password = 'admin123';
    $email = 'admin@example.com';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $result = $stmt->execute([$username, $hashedPassword, $email]);
    
    if ($result) {
        echo "<p>✅ New admin account created successfully!</p>";
        echo "<p><strong>Username:</strong> $username</p>";
        echo "<p><strong>Password:</strong> $password</p>";
        echo "<p><strong>Password Hash:</strong> $hashedPassword</p>";
    } else {
        echo "<p>❌ Failed to create admin account</p>";
    }
    
    echo "<h2>3. Testing Login...</h2>";
    
    // Test the login
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        echo "<p style='color: green; font-size: 18px;'><strong>🎉 SUCCESS! Login will work now!</strong></p>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>Login Credentials:</h3>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ Login test failed. There might be another issue.</p>";
    }
    
    echo "<h2>4. Adding Sample Users...</h2>";
    
    // Check if sample users exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    if ($userCount == 0) {
        echo "<p>Adding sample users...</p>";
        
        $sampleUsers = [
            ['John Doe', 'john@example.com', '1234567890', 25, '123 Main St, City', 'active', '2025-01-01 10:00:00', '2025-07-20 09:30:00'],
            ['Jane Smith', 'jane@example.com', '0987654321', 30, '456 Oak Ave, Town', 'active', '2025-02-15 14:20:00', '2025-07-22 16:45:00'],
            ['Bob Johnson', 'bob@example.com', '5551234567', 45, '789 Pine Rd, Village', 'inactive', '2024-12-10 08:15:00', '2025-06-30 11:20:00'],
            ['Alice Brown', 'alice@example.com', '7778889999', 28, '321 Elm St, County', 'active', '2025-03-20 11:30:00', '2025-07-24 08:15:00'],
            ['Charlie Wilson', 'charlie@example.com', '4445556666', 55, '654 Maple Dr, District', 'active', '2024-11-05 16:45:00', '2025-07-15 14:30:00']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, age, address, status, registration_date, last_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleUsers as $user) {
            $stmt->execute($user);
        }
        
        echo "<p>✅ Sample users added successfully!</p>";
    } else {
        echo "<p>✅ Sample users already exist ($userCount users found)</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li><a href='login.php' style='color: #007bff; text-decoration: none;'>🔑 Go to Login Page</a></li>";
echo "<li><a href='index.php' style='color: #007bff; text-decoration: none;'>🏠 Back to Home</a></li>";
echo "<li><a href='dashboard.php' style='color: #007bff; text-decoration: none;'>📊 Direct to Dashboard</a></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: #6c757d;'><strong>Security Note:</strong> Delete this file (setup_database.php) after successful setup for security reasons.</p>";
?>

<style>
body { 
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
    margin: 40px; 
    background: #f8f9fa;
    color: #333;
}
h1, h2 { color: #495057; }
h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { border-bottom: 1px solid #dee2e6; padding-bottom: 5px; margin-top: 30px; }
p { margin: 10px 0; }
ol, ul { margin: 15px 0; }
li { margin: 5px 0; }
hr { margin: 30px 0; border: none; border-top: 2px solid #dee2e6; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
