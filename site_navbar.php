<?php
if(!isset($_SESSION)) session_start();
$role = 'public';
if(!empty($_SESSION['admin_logged_in'])) $role = 'admin';
elseif(!empty($_SESSION['seller_logged_in'])) $role = 'seller';
?>
<nav class="navbar">
  <div class="container-sm d-flex align-items-center">
    <a class="navbar-brand" href="<?= $role==='admin' ? 'admin_panel.php' : ($role==='seller' ? 'seller_dashboard.php' : 'index.html') ?>">
      <i class="bi bi-printer"></i> ST PRINT
    </a>

    <div class="ms-auto d-flex align-items-center" style="gap:10px;">
      <a href="track_order.php" class="track-btn d-flex align-items-center" style="gap:8px;">
        <i class="bi bi-search"></i> Track Order
      </a>

      <?php if($role==='admin'): ?>
        <span class="text-muted-small">Hello, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        <a href="admin_sellers.php" class="btn btn-sm-custom" style="background:linear-gradient(45deg,#00BFFF,#FF1493); color:#fff;"><i class="bi bi-people"></i> Sellers</a>
        <a href="change_password.php" class="btn btn-sm-custom"><i class="bi bi-key"></i> Change Password</a>
        <a href="logout.php" class="btn btn-sm-custom"><i class="bi bi-box-arrow-right"></i> Logout</a>
      <?php elseif($role==='seller'): ?>
        <span class="text-muted-small"><?= htmlspecialchars($_SESSION['seller_name'] ?? 'Seller') ?></span>
        <a href="seller_change_password.php" class="btn btn-sm-custom"><i class="bi bi-key"></i> Change Password</a>
        <a href="seller_logout.php" class="btn btn-sm-custom"><i class="bi bi-box-arrow-right"></i> Logout</a>
      <?php else: ?>
        <a href="admin_login.php" class="btn btn-sm-custom" style="background:transparent;border:1px solid rgba(12,18,24,0.06);"><i class="bi bi-shield-lock"></i> Admin</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
