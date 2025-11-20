<?php
/**
 * install_admin.php - Reset Admin Credentials
 * DELETE THIS FILE after running!
 */

require_once 'config.php';

echo "<h2>🔧 ST PRINT - Admin Reset</h2>";
echo "<hr>";

try {
    $db = db_connect();
    
    // Step 1: Drop table
    echo "<p>Step 1: Dropping admin_users table...</p>";
    $db->query("DROP TABLE IF EXISTS admin_users");
    echo "<p style='color:green;'>✅ Table dropped</p>";
    
    // Step 2: Create table
    echo "<p>Step 2: Creating admin_users table...</p>";
    $create_sql = "CREATE TABLE admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(191) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        fullname VARCHAR(191),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->query($create_sql);
    echo "<p style='color:green;'>✅ Table created</p>";
    
    // Step 3: Insert admin
    echo "<p>Step 3: Creating admin user...</p>";
    
    $username = 'admin';
    $password = 'admin123';
    $fullname = 'Site Administrator';
    
    // Create hash
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash, fullname) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $hash, $fullname);
    $stmt->execute();
    
    echo "<p style='color:green;'>✅ Admin user created</p>";
    
    $stmt->close();
    
    // Verify it works
    echo "<p>Step 4: Verifying...</p>";
    $verify_stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE username=?");
    $verify_stmt->bind_param('s', $username);
    $verify_stmt->execute();
    $verify_stmt->bind_result($stored_hash);
    $verify_stmt->fetch();
    
    if(password_verify($password, $stored_hash)) {
        echo "<p style='color:green;'>✅ Password verification SUCCESS!</p>";
    } else {
        echo "<p style='color:red;'>❌ Verification failed</p>";
    }
    
    $verify_stmt->close();
    $db->close();
    
    // Success message
    echo "<div style='background:#d4edda; padding:20px; border-radius:5px; margin-top:20px;'>";
    echo "<h3 style='color:green;'>✅ Admin Reset Complete!</h3>";
    echo "<p><strong>Login Credentials:</strong></p>";
    echo "<p>Username: <code>admin</code></p>";
    echo "<p>Password: <code>admin123</code></p>";
    echo "<p><a href='admin_login.php' style='color:blue; font-size:18px;'><strong>→ Go to Admin Login</strong></a></p>";
    echo "<p style='color:red;'><strong>⚠️ DELETE install_admin.php for security!</strong></p>";
    echo "</div>";
    
} catch(Exception $e) {
    echo "<div style='background:#f8d7da; padding:20px; border-radius:5px;'>";
    echo "<p style='color:red;'><strong>❌ Error: " . htmlspecialchars($e->getMessage()) . "</strong></p>";
    echo "</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ST PRINT - Admin Reset</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        code { background: #f0f0f0; padding: 3px 8px; border-radius: 3px; }
    </style>
</head>
<body></body>
</html>