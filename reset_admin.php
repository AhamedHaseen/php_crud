<?php
// Emergency Admin Password Reset
// Run this file ONCE to fix the login issue

require_once 'config.php';

echo "<h1>🔧 Emergency Admin Password Reset</h1>";

try {
    // Generate a fresh password hash for 'admin123'
    $username = 'admin';
    $plainPassword = 'admin123';
    $email = 'admin@example.com';
    
    // Generate new hash
    $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🔑 New Password Details:</h3>";
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> $plainPassword</p>";
    echo "<p><strong>New Hash:</strong> $newHash</p>";
    echo "</div>";
    
    // Delete existing admin and create new one
    $pdo->exec("DELETE FROM admins WHERE username = 'admin'");
    
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $result = $stmt->execute([$username, $newHash, $email]);
    
    if ($result) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; color: #155724;'>";
        echo "<h3>✅ SUCCESS!</h3>";
        echo "<p>Admin account has been reset successfully!</p>";
        echo "</div>";
        
        // Test the password verification
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($plainPassword, $admin['password'])) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; color: #155724;'>";
            echo "<h3>🎉 LOGIN TEST PASSED!</h3>";
            echo "<p>You can now login with: <strong>admin</strong> / <strong>admin123</strong></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
            echo "<h3>❌ LOGIN TEST FAILED!</h3>";
            echo "<p>There might be another issue. Please check your PHP version.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
        echo "<h3>❌ FAILED TO CREATE ADMIN</h3>";
        echo "</div>";
    }
    
    // Show current admin table contents
    echo "<h3>📋 Current Admin Table:</h3>";
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM admins");
    $admins = $stmt->fetchAll();
    
    if ($admins) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No admin users found in database.</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
    echo "<h3>❌ ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🚀 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='login.php' style='color: #007bff;'>🔑 Try Login Now</a></li>";
echo "<li><a href='dashboard.php' style='color: #007bff;'>📊 Go to Dashboard</a></li>";
echo "<li><strong>Delete this file after successful login for security!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: #6c757d;'>";
echo "<strong>Why this happened:</strong> The password hash in your database was not compatible with your PHP version's password_verify() function. ";
echo "This script generates a fresh hash that will work with your current PHP setup.";
echo "</p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: #f8f9fa;
}
h1 { color: #495057; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h3 { color: #495057; }
table { margin: 20px 0; }
th, td { padding: 12px; text-align: left; }
th { background: #e9ecef; }
a { text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
