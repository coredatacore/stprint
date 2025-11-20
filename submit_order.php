<?php
header("Content-Type: application/json");
require_once 'config.php';

try {
    $db = db_connect();
    
    $name          = trim($_POST['name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $department    = trim($_POST['department'] ?? '');
    $year_level    = trim($_POST['year_level'] ?? '');
    $print_type    = trim($_POST['print_type'] ?? '');
    $design_option = trim($_POST['design_option'] ?? '');
    $quantity      = (int)($_POST['quantity'] ?? 0);
    $price         = (float)($_POST['price'] ?? 0);  // ✅ ADD THIS LINE
    $total_price   = $_POST['total_price'] ?? '';
    $description   = trim($_POST['description'] ?? '');

    $total_price = (float)str_replace("₱", "", $total_price);
    $price = (float)str_replace("₱", "", $price);  // ✅ ADD THIS LINE

    if (!$name || !$email || !$department || !$year_level || !$print_type || $quantity <= 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Please fill all required fields."
        ]);
        exit;
    }

    // ✅ FIXED: Added price column
    $stmt = $db->prepare("
        INSERT INTO orders 
        (name, email, department, year_level, print_type, design_option, price, quantity, total_price, description, status) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $db->error);
    }

    $stmt->bind_param(
        'ssssssddds',
        $name,
        $email,
        $department,
        $year_level,
        $print_type,
        $design_option,
        $price,              // ✅ ADD THIS
        $quantity,
        $total_price,
        $description
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert order: " . $stmt->error);
    }

    $order_id = $db->insert_id;

    echo json_encode([
        "status" => "success",
        "order_id" => $order_id
    ]);

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    error_log("Order submission error: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Failed to submit order. Please try again."
    ]);
}
?>