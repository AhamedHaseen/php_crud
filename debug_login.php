<?php
// Debug Login Issues - Check Admin Table and Password Verification
require_once 'config.php';

echo "<h1>🔍 Login Debug Tool</h1>";

try {
    // Check if admin table exists and has data
    echo "<h2>1. Checking Admin Table</h2>";
    
    $stmt = $pdo->query("SELECT * FROM admins");
    $admins = $stmt->fetchAll();
    
    if (empty($admins)) {
        echo "<p style='color: red;'>❌ <strong>PROBLEM FOUND:</strong> No admin users in database!</p>";
        echo "<p>Let's create one now...</p>";
        
        // Create admin user with correct hash
        $username = 'admin';
        $password = 'admin123';
        $email = 'admin@example.com';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $hash, $email]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Admin table has " . count($admins) . " user(s)</p>";
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>Email</th><th>Password Hash</th></tr>";
        
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars(substr($admin['password'], 0, 30)) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>2. Testing Password Verification</h2>";
    
    // Get the admin user
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        $testPassword = 'admin123';
        $storedHash = $admin['password'];
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>Testing Details:</h3>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($admin['username']) . "</p>";
        echo "<p><strong>Test Password:</strong> " . htmlspecialchars($testPassword) . "</p>";
        echo "<p><strong>Stored Hash:</strong> " . htmlspecialchars($storedHash) . "</p>";
        echo "</div>";
        
        // Test password verification
        if (password_verify($testPassword, $storedHash)) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
            echo "<h3>🎉 PASSWORD VERIFICATION SUCCESS!</h3>";
            echo "<p>The login should work with: <strong>admin</strong> / <strong>admin123</strong></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
            echo "<h3>❌ PASSWORD VERIFICATION FAILED!</h3>";
            echo "<p>The stored hash doesn't match the password 'admin123'</p>";
            echo "<p><strong>Solution:</strong> Let's update the password hash...</p>";
            echo "</div>";
            
            // Update with correct hash
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
            $result = $stmt->execute([$newHash, 'admin']);
            
            if ($result) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
                echo "<h3>✅ PASSWORD UPDATED!</h3>";
                echo "<p>New hash: " . htmlspecialchars($newHash) . "</p>";
                
                // Test again
                if (password_verify($testPassword, $newHash)) {
                    echo "<p><strong>✅ VERIFICATION NOW WORKS!</strong></p>";
                } else {
                    echo "<p style='color: red;'>❌ Still not working - there might be a PHP issue</p>";
                }
                echo "</div>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No admin user found with username 'admin'</p>";
    }
    
    echo "<h2>3. Testing Database Connection</h2>";
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p>✅ MySQL Version: " . htmlspecialchars($version['version']) . "</p>";
    echo "<p>✅ PHP Version: " . phpversion() . "</p>";
    
    echo "<h2>4. Testing Session</h2>";
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<p>✅ Session is active</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Session not active</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>❌ ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>🚀 Next Steps:</h2>";
echo "<ol>";
echo "<li><a href='login.php'>🔑 Try Login Again</a></li>";
echo "<li><a href='dashboard.php'>📊 Direct to Dashboard</a></li>";
echo "<li><strong>Delete this debug file after fixing the issue</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: #666;'><em>This debug tool shows exactly what's happening with your login system.</em></p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f8f9fa;
    line-height: 1.6;
}
h1, h2 { color: #495057; }
h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { border-bottom: 1px solid #dee2e6; padding-bottom: 5px; margin-top: 30px; }
table { width: 100%; margin: 20px 0; }
th, td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
th { background: #e9ecef; font-weight: 600; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>
