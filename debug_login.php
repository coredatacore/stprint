<?php
/**
 * Debug script to troubleshoot login issues
 */

require_once 'config.php';

echo "<h2>🔍 ST PRINT - Login Debugger</h2>";
echo "<hr>";

// Test 1: Database Connection
echo "<h3>Test 1: Database Connection</h3>";
try {
    $db = db_connect();
    echo "<p style='color:green;'>✅ Connected to database successfully</p>";
} catch(Exception $e) {
    echo "<p style='color:red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check if admin_users table exists
echo "<h3>Test 2: Check admin_users Table</h3>";
$result = $db->query("SHOW TABLES LIKE 'admin_users'");
if($result->num_rows > 0) {
    echo "<p style='color:green;'>✅ admin_users table exists</p>";
} else {
    echo "<p style='color:red;'>❌ admin_users table NOT FOUND!</p>";
    echo "<p>Run: <code>php install_admin.php</code></p>";
    $db->close();
    exit;
}

// Test 3: Check admin user exists
echo "<h3>Test 3: Check Admin User</h3>";
$stmt = $db->prepare("SELECT id, username, password_hash, fullname FROM admin_users WHERE username=?");
if(!$stmt) {
    echo "<p style='color:red;'>❌ Prepare failed: " . $db->error . "</p>";
    $db->close();
    exit;
}

$username = 'admin';
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<p style='color:red;'>❌ Admin user NOT FOUND!</p>";
    echo "<p>Run: <code>php install_admin.php</code></p>";
    $stmt->close();
    $db->close();
    exit;
} else {
    echo "<p style='color:green;'>✅ Admin user found</p>";
}

$row = $result->fetch_assoc();
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Value</th></tr>";
echo "<tr><td>ID</td><td>" . htmlspecialchars($row['id']) . "</td></tr>";
echo "<tr><td>Username</td><td>" . htmlspecialchars($row['username']) . "</td></tr>";
echo "<tr><td>Full Name</td><td>" . htmlspecialchars($row['fullname']) . "</td></tr>";
echo "<tr><td>Password Hash</td><td><code>" . htmlspecialchars($row['password_hash']) . "</code></td></tr>";
echo "</table>";

// Test 4: Test password verification
echo "<h3>Test 4: Password Verification</h3>";
$test_password = 'admin123';
$stored_hash = $row['password_hash'];

echo "<p>Testing password: <code>$test_password</code></p>";
echo "<p>Stored hash: <code>" . substr($stored_hash, 0, 20) . "...</code></p>";

if(password_verify($test_password, $stored_hash)) {
    echo "<p style='color:green;'>✅ Password verification SUCCESSFUL!</p>";
    echo "<p><strong>You should be able to login with:</strong></p>";
    echo "<p>Username: <code>admin</code></p>";
    echo "<p>Password: <code>admin123</code></p>";
} else {
    echo "<p style='color:red;'>❌ Password verification FAILED!</p>";
    echo "<p>The password hash in database may be corrupted.</p>";
    echo "<p>Solution: Run <code>install_admin.php</code> to reset</p>";
}

// Test 5: Simulate login process
echo "<h3>Test 5: Simulate Login Process</h3>";
$test_username = 'admin';
$test_password = 'admin123';

$stmt2 = $db->prepare("SELECT id, password_hash, fullname FROM admin_users WHERE username=? LIMIT 1");
if(!$stmt2) {
    echo "<p style='color:red;'>❌ Prepare failed: " . $db->error . "</p>";
} else {
    $stmt2->bind_param('s', $test_username);
    $stmt2->execute();
    $stmt2->store_result();
    
    echo "<p>Rows found: " . $stmt2->num_rows . "</p>";
    
    if($stmt2->num_rows === 1) {
        $stmt2->bind_result($id, $hash, $fullname);
        $stmt2->fetch();
        
        if(password_verify($test_password, $hash)) {
            echo "<p style='color:green;'>✅ Login simulation SUCCESSFUL!</p>";
            echo "<p>Session would be set with:</p>";
            echo "<p>admin_logged_in: true</p>";
            echo "<p>admin_id: $id</p>";
            echo "<p>admin_name: " . htmlspecialchars($fullname) . "</p>";
        } else {
            echo "<p style='color:red;'>❌ Login simulation FAILED (password mismatch)</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Login simulation FAILED (user not found)</p>";
    }
    $stmt2->close();
}

// Test 6: Database Configuration
echo "<h3>Test 6: Database Configuration</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Host</td><td>" . getenv('DB_HOST') ?: 'localhost' . "</td></tr>";
echo "<tr><td>User</td><td>" . getenv('DB_USER') ?: 'root' . "</td></tr>";
echo "<tr><td>Database</td><td>" . getenv('DB_NAME') ?: 'st_print' . "</td></tr>";
echo "</table>";

$db->close();

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If all tests pass ✅: Try logging in at <a href='admin_login.php'>admin_login.php</a></li>";
echo "<li>If password verification fails ❌: Run <a href='install_admin.php'>install_admin.php</a> to reset</li>";
echo "<li>If table not found ❌: Run <code>create_tables.sql</code></li>";
echo "</ol>";

echo "<p style='color:red;'><strong>⚠️ Delete this file (debug_login.php) after troubleshooting for security!</strong></p>";
?>