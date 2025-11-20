<?php
require_once 'config.php';
if(!isset($_GET['name'],$_GET['department'],$_GET['year_level'])) die('Invalid request');
$name = trim($_GET['name']); 
$department = $_GET['department']; 
$year = $_GET['year_level'];
try{
  $db = db_connect();
  $stmt = $db->prepare("SELECT * FROM orders WHERE name LIKE CONCAT('%',?,'%') AND department=? AND year_level=? ORDER BY created_at DESC");
  $stmt->bind_param('sss', $name, $department, $year);
  $stmt->execute();
  $res = $stmt->get_result();
}catch(Exception $e){ die('Error'); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar"><div class="container-sm d-flex align-items-center"><a class="navbar-brand" href="index.php">ST PRINT</a></div></nav>
<div class="container-sm my-4">
  <div class="mb-3">
    <h4>Search results for <strong><?= htmlspecialchars($name) ?></strong></h4>
    <p class="text-muted-small"><?= htmlspecialchars($department) ?> • <?= htmlspecialchars($year) ?></p>
  </div>

  <?php if($res->num_rows===0): ?>
    <div class="alert alert-danger">No orders found.</div>
  <?php else: ?>
    <?php while($o=$res->fetch_assoc()): ?>
      <div class="card mb-3">
        <div style="padding:12px;">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <strong>Order #<?= $o['id'] ?></strong>
              <div class="text-muted-small"><?= $o['created_at'] ?></div>
            </div>
            <div>
              <?php if($o['status']==='pending'): ?>
                <span class="badge-status badge bg-warning text-dark">Pending</span>
              <?php elseif($o['status']==='sold'): ?>
                <span class="badge-status badge bg-success">Sold</span>
              <?php elseif($o['status']==='returned'): ?>
                <span class="badge-status badge bg-secondary">Returned</span>
              <?php else: ?>
                <span class="badge-status badge bg-danger">Cancelled</span>
              <?php endif; ?>
            </div>
          </div>

          <hr>
          <p><strong>Item:</strong> <?= htmlspecialchars($o['print_type']) ?> • Qty: <?= $o['quantity'] ?></p>
          <p><strong>Total:</strong> ₱<?= number_format($o['total_price'],2) ?></p>
          <p class="text-muted-small"><?= nl2br(htmlspecialchars($o['description'])) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>

</div>
</body>
</html>