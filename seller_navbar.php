<?php
if(!isset($_SESSION)) session_start();
?>
<nav class="navbar">
  <div class="container-sm d-flex align-items-center">
    <a class="navbar-brand" href="seller_dashboard.php"><i class="bi bi-shop"></i> Seller</a>

    <div class="ms-auto d-flex align-items-center" style="gap:10px;">
      <span class="text-muted-small"><?= htmlspecialchars($_SESSION['seller_name'] ?? 'Seller') ?></span>

      <!-- Track Order available to sellers -->
      <a href="track_order.php" class="btn btn-sm-custom"><i class="bi bi-search"></i> Track</a>

      <a href="seller_change_password.php" class="btn btn-sm-custom"><i class="bi bi-key"></i> Change Password</a>
      <a href="seller_logout.php" class="btn btn-sm-custom"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>
</nav>
