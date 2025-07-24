<?php
// Password Hash Generator for Admin User
// This script will generate the correct password hash and update the admin user

// Include database configuration
require_once 'config.php';

echo "<h2>Admin Password Hash Generator & Updater</h2>";

// Generate the correct password hash for 'admin123'
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "<p><strong>Password:</strong> " . $password . "</p>";
echo "<p><strong>Generated Hash:</strong> " . $hashedPassword . "</p>";

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    $existingAdmin = $stmt->fetch();
    
    if ($existingAdmin) {
        echo "<p><strong>Status:</strong> Admin user exists</p>";
        
        // Update the admin user with correct password hash
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $result = $stmt->execute([$hashedPassword, 'admin']);
        
        if ($result) {
            echo "<p style='color: green;'><strong>✅ SUCCESS:</strong> Admin password updated successfully!</p>";
        } else {
            echo "<p style='color: red;'><strong>❌ ERROR:</strong> Failed to update admin password</p>";
        }
    } else {
        echo "<p><strong>Status:</strong> Admin user does not exist. Creating new admin user...</p>";
        
        // Insert new admin user
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $result = $stmt->execute(['admin', $hashedPassword, 'admin@example.com']);
        
        if ($result) {
            echo "<p style='color: green;'><strong>✅ SUCCESS:</strong> Admin user created successfully!</p>";
        } else {
            echo "<p style='color: red;'><strong>❌ ERROR:</strong> Failed to create admin user</p>";
        }
    }
    
    // Verify the login works
    echo "<hr>";
    echo "<h3>Testing Login Verification:</h3>";
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify('admin123', $admin['password'])) {
        echo "<p style='color: green;'><strong>✅ LOGIN TEST PASSED:</strong> Username 'admin' with password 'admin123' will work!</p>";
    } else {
        echo "<p style='color: red;'><strong>❌ LOGIN TEST FAILED:</strong> There's still an issue with the login credentials</p>";
    }
    
    // Display admin user details
    echo "<hr>";
    echo "<h3>Current Admin User Details:</h3>";
    if ($admin) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr>";
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td>" . $admin['username'] . "</td>";
        echo "<td>" . $admin['email'] . "</td>";
        echo "<td>" . $admin['created_at'] . "</td>";
        echo "</tr>";
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ DATABASE ERROR:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If you see '✅ LOGIN TEST PASSED', you can now login with:</li>";
echo "<ul><li><strong>Username:</strong> admin</li><li><strong>Password:</strong> admin123</li></ul>";
echo "<li>Go to <a href='login.php'>login.php</a> to test the login</li>";
echo "<li>After successful login, you can delete this file for security</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='login.php'>Go to Login →</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
table { border-collapse: collapse; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>
