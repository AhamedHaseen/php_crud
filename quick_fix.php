<?php
// One-Click Password Fix
require_once 'config.php';

echo "<h1>🔧 One-Click Password Fix</h1>";

try {
    // Generate a fresh hash for admin123
    $correctPassword = 'admin123';
    $newHash = password_hash($correctPassword, PASSWORD_DEFAULT);
    
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🔑 Fixing Admin Password...</h3>";
    echo "<p><strong>Password:</strong> $correctPassword</p>";
    echo "<p><strong>New Hash:</strong> " . substr($newHash, 0, 30) . "...</p>";
    echo "</div>";
    
    // Delete and recreate admin user
    $pdo->exec("DELETE FROM admins WHERE username = 'admin'");
    
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $result = $stmt->execute(['admin', $newHash, 'admin@example.com']);
    
    if ($result) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; color: #155724;'>";
        echo "<h3>✅ FIXED SUCCESSFULLY!</h3>";
        
        // Test the verification immediately
        if (password_verify($correctPassword, $newHash)) {
            echo "<p><strong>🎉 Password verification works!</strong></p>";
            echo "<p>You can now login with:</p>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> admin</li>";
            echo "<li><strong>Password:</strong> admin123</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ Something is still wrong with password verification</p>";
        }
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
        echo "<h3>❌ Failed to create admin user</h3>";
        echo "</div>";
    }
    
    // Show the admin table
    echo "<h3>📋 Current Admin Table:</h3>";
    $stmt = $pdo->query("SELECT id, username, email, LEFT(password, 20) as password_preview FROM admins");
    $admins = $stmt->fetchAll();
    
    if ($admins) {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Username</th><th>Email</th><th>Password Preview</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td style='font-family: monospace;'>" . htmlspecialchars($admin['password_preview']) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; color: #721c24;'>";
    echo "<h3>❌ ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; margin: 10px;'>";
echo "<i class='fas fa-sign-in-alt'></i> Try Login Now";
echo "</a>";
echo "<a href='dashboard.php' style='background: #28a745; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; margin: 10px;'>";
echo "<i class='fas fa-tachometer-alt'></i> Go to Dashboard";
echo "</a>";
echo "</div>";

echo "<p style='color: #6c757d; text-align: center;'>";
echo "<small>Delete this file after successful login for security.</small>";
echo "</p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    max-width: 700px;
    margin: 40px auto;
    padding: 20px;
    background: #f8f9fa;
}
h1, h3 { color: #495057; }
h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; }
table { width: 100%; margin: 20px 0; }
th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
th { background: #e9ecef; }
hr { margin: 30px 0; }
</style>
