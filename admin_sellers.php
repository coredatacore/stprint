<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php'); exit;
}
$db = db_connect();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_seller'])) {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = $_POST['password'];

    if ($username && $password && $fullname) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO sellers (username,password_hash,full_name) VALUES (?,?,?)");
        $stmt->bind_param('sss', $username, $hash, $fullname);
        try { 
            $stmt->execute(); 
            $msg = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> <span>Seller created successfully!</span></div>"; 
        } catch(Exception $e){ 
            $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-circle-fill'></i> <span>Error: ".$e->getMessage()."</span></div>"; 
        }
        $stmt->close();
    } else $msg = "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill'></i> <span>Fill all fields.</span></div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_item'])) {
    $seller_id = intval($_POST['seller_id']);
    $item_code = trim($_POST['item_code']);
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $commission = floatval($_POST['commission']);
    $quantity = intval($_POST['quantity']);

    if ($seller_id && $item_code && $product_name && $commission >= 0) {
        $stmt = $db->prepare("INSERT INTO seller_items (seller_id,item_code,product_name,price,commission_rate,quantity) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('issdd i', $seller_id, $item_code, $product_name, $price, $commission, $quantity);
        try { 
            $stmt->execute(); 
            $msg = "<div class='alert alert-success'><i class='bi bi-check-circle-fill'></i> <span>Item assigned successfully!</span></div>"; 
        } catch(Exception $e){ 
            $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-circle-fill'></i> <span>Error: ".$e->getMessage()."</span></div>"; 
        }
        $stmt->close();
    } else $msg = "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill'></i> <span>Fill all item fields.</span></div>";
}

$sellers = $db->query("SELECT * FROM sellers ORDER BY created_at DESC");
$items = $db->query("SELECT si.*, s.username FROM seller_items si LEFT JOIN sellers s ON si.seller_id = s.id ORDER BY si.created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Sellers - ST PRINT Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      font-family: "Inter", "Segoe UI", sans-serif;
      min-height: 100vh;
      color: #2c2c2c;
    }

    /* ====== NAVBAR ====== */
    .navbar-custom {
      background: linear-gradient(135deg, #ffffff 0%, #f5f7fa 100%);
      border-bottom: 3px solid #00C6FF;
      box-shadow: 0 4px 15px rgba(0, 198, 255, 0.15);
      padding: 15px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-brand-custom {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 900;
      font-size: 1.5rem;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .navbar-brand-custom:hover {
      transform: scale(1.05);
    }

    .nav-buttons {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .nav-btn {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      color: white !important;
      padding: 10px 16px !important;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9rem;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
    }

    .nav-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.3);
      color: white;
    }

    /* ====== ALERTS ====== */
    .alert {
      border-radius: 12px;
      border: none;
      padding: 15px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert-success {
      background: rgba(16, 185, 129, 0.1);
      border-left: 4px solid #10b981;
      color: #10b981;
    }

    .alert-danger {
      background: rgba(239, 68, 68, 0.1);
      border-left: 4px solid #ef4444;
      color: #ef4444;
    }

    .alert-warning {
      background: rgba(245, 158, 11, 0.1);
      border-left: 4px solid #f59e0b;
      color: #f59e0b;
    }

    .alert i {
      font-size: 1.2rem;
    }

    /* ====== MAIN CONTAINER ====== */
    .main-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .page-title {
      font-size: 2rem;
      font-weight: 800;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 40px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* ====== CARDS ====== */
    .card-custom {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.1);
      margin-bottom: 25px;
      transition: all 0.3s ease;
      border: none;
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 40px rgba(0, 198, 255, 0.2);
    }

    .card-header-custom {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      color: white;
      padding: 20px;
      border-radius: 15px 15px 0 0;
      font-weight: 700;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 10px;
      margin: -30px -30px 20px -30px;
    }

    /* ====== FORMS ====== */
    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      font-weight: 700;
      color: #2c2c2c;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-label i {
      color: #00C6FF;
      font-size: 1.1rem;
    }

    .form-control, .form-select {
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .form-control:focus, .form-select:focus {
      border-color: #00C6FF;
      background: white;
      box-shadow: 0 0 0 0.3rem rgba(0, 198, 255, 0.15);
      outline: none;
    }

    .form-control::placeholder {
      color: #bbb;
    }

    .password-wrapper {
      position: relative;
    }

    .form-control.has-password {
      padding-right: 40px;
    }

    .password-toggle {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #00C6FF;
      font-size: 1.1rem;
      user-select: none;
      transition: all 0.2s ease;
      z-index: 10;
    }

    .password-toggle:hover {
      transform: translateY(-50%) scale(1.2);
      color: #FF57B9;
    }

    /* ====== BUTTONS ====== */
    .btn-submit {
      width: 100%;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      padding: 14px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.25);
      margin-top: 10px;
    }

    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0, 198, 255, 0.35);
      color: white;
    }

    /* ====== LIST ITEMS ====== */
    .list-item {
      padding: 15px;
      border-bottom: 1px solid rgba(0, 198, 255, 0.1);
      transition: all 0.3s ease;
    }

    .list-item:hover {
      background: rgba(0, 198, 255, 0.05);
      border-radius: 8px;
    }

    .list-item:last-child {
      border-bottom: none;
    }

    .list-item-title {
      font-weight: 700;
      color: #2c2c2c;
      margin-bottom: 5px;
    }

    .list-item-subtitle {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 8px;
    }

    .badge-custom {
      display: inline-block;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      color: white;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-right: 6px;
    }

    /* ====== EMPTY STATE ====== */
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #999;
    }

    .empty-state i {
      font-size: 3rem;
      color: #ddd;
      display: block;
      margin-bottom: 15px;
    }

    /* ====== GRID ====== */
    .grid-2 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
      gap: 25px;
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
      .page-title {
        font-size: 1.5rem;
      }

      .card-custom {
        padding: 20px;
      }

      .card-header-custom {
        margin: -20px -20px 15px -20px;
        font-size: 1rem;
      }

      .grid-2 {
        grid-template-columns: 1fr;
      }

      .nav-buttons {
        gap: 8px;
      }

      .nav-btn {
        padding: 8px 12px !important;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 480px) {
      .main-container {
        margin: 20px auto;
      }

      .page-title {
        font-size: 1.3rem;
        gap: 8px;
      }

      .navbar-brand-custom {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-custom">
  <div class="container-sm d-flex align-items-center justify-content-between">
    <a class="navbar-brand-custom" href="admin_panel.php">
      <i class="bi bi-printer-fill"></i> ST PRINT
    </a>

    <div class="nav-buttons">
      <a href="admin_panel.php" class="nav-btn">
        <i class="bi bi-arrow-left"></i> Orders
      </a>
      <a href="change_password.php" class="nav-btn">
        <i class="bi bi-key"></i> Password
      </a>
      <a href="logout.php" class="nav-btn">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="main-container">
  <h1 class="page-title">
    <i class="bi bi-people-fill"></i> Manage Sellers
  </h1>

  <!-- ALERTS -->
  <?= $msg ?>

  <!-- GRID -->
  <div class="grid-2">
    <!-- CREATE SELLER SECTION -->
    <div>
      <div class="card-custom">
        <div class="card-header-custom">
          <i class="bi bi-plus-circle"></i> Create New Seller
        </div>

        <form method="post">
          <input type="hidden" name="create_seller" value="1">

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-person-badge"></i> Username
            </label>
            <input name="username" class="form-control" placeholder="e.g., seller001" required>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-person-check"></i> Full Name
            </label>
            <input name="fullname" class="form-control" placeholder="e.g., John Doe" required>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-lock-fill"></i> Password
            </label>
            <div class="password-wrapper">
              <input type="password" name="password" class="form-control has-password" placeholder="Enter password" id="sellerPassword" required>
              <span class="password-toggle" id="toggleSellerPassword">
                <i class="bi bi-eye-fill"></i>
              </span>
            </div>
          </div>

          <button type="submit" class="btn-submit">
            <i class="bi bi-plus-lg"></i> Create Seller
          </button>
        </form>
      </div>

      <!-- SELLERS LIST -->
      <div class="card-custom">
        <div class="card-header-custom">
          <i class="bi bi-list-ul"></i> Active Sellers
        </div>

        <?php 
          $seller_count = 0;
          $sellers->data_seek(0);
          while($s = $sellers->fetch_assoc()): 
            $seller_count++;
        ?>
          <div class="list-item">
            <div class="list-item-title">
              <i class="bi bi-person-fill"></i> <?=htmlspecialchars($s['username'])?>
            </div>
            <div class="list-item-subtitle">
              <?=htmlspecialchars($s['full_name'])?>
            </div>
            <span class="badge-custom">
              <i class="bi bi-calendar"></i> <?=$s['created_at']?>
            </span>
          </div>
        <?php endwhile; ?>

        <?php if($seller_count === 0): ?>
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No sellers created yet</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ASSIGN ITEM SECTION -->
    <div>
      <div class="card-custom">
        <div class="card-header-custom">
          <i class="bi bi-box-seam"></i> Assign Item to Seller
        </div>

        <form method="post">
          <input type="hidden" name="assign_item" value="1">

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-person-check"></i> Select Seller
            </label>
            <select name="seller_id" class="form-select" required>
              <option value="">-- Choose a seller --</option>
              <?php 
                $sellers->data_seek(0);
                while($s = $sellers->fetch_assoc()): 
              ?>
                <option value="<?= $s['id'] ?>">
                  <?= htmlspecialchars($s['username'].' — '.$s['full_name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-barcode"></i> Item Code
            </label>
            <input name="item_code" class="form-control" placeholder="e.g., ITEM-001" required>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-bag"></i> Product Name
            </label>
            <input name="product_name" class="form-control" placeholder="e.g., Button Pin" required>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-cash-coin"></i> Price (₱)
                </label>
                <input name="price" type="number" step="0.01" class="form-control" placeholder="45.00" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-percent"></i> Commission (%)
                </label>
                <input name="commission" type="number" step="0.01" value="0" class="form-control" placeholder="e.g., 10.00" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-stack"></i> Quantity
            </label>
            <input name="quantity" type="number" value="1" class="form-control" required>
          </div>

          <button type="submit" class="btn-submit">
            <i class="bi bi-check-lg"></i> Assign Item
          </button>
        </form>
      </div>

      <!-- ASSIGNED ITEMS LIST -->
      <div class="card-custom">
        <div class="card-header-custom">
          <i class="bi bi-list-check"></i> Assigned Items
        </div>

        <?php 
          $item_count = 0;
          $items->data_seek(0);
          while($it = $items->fetch_assoc()): 
            $item_count++;
            $commission_amount = ($it['price'] * $it['commission_rate']) / 100;
        ?>
          <div class="list-item">
            <div class="list-item-title">
              <i class="bi bi-box-fill"></i> <?=htmlspecialchars($it['item_code'])?>
            </div>
            <div class="list-item-subtitle">
              <?=htmlspecialchars($it['product_name'])?>
            </div>
            <div>
              <span class="badge-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                💰 ₱<?=number_format($it['price'],2)?>
              </span>
              <span class="badge-custom" style="background: linear-gradient(135deg, #5b7cfa 0%, #b197fc 100%);">
                👤 <?=htmlspecialchars($it['username'])?>
              </span>
              <span class="badge-custom" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                📦 ×<?= (int)$it['quantity'] ?>
              </span>
              <span class="badge-custom" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                💼 <?=$it['commission_rate']?>% (₱<?=number_format($commission_amount,2)?>)
              </span>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if($item_count === 0): ?>
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No items assigned yet</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // Password Toggle
  const toggleSellerPassword = document.getElementById('toggleSellerPassword');
  const sellerPassword = document.getElementById('sellerPassword');

  if(toggleSellerPassword && sellerPassword) {
    toggleSellerPassword.addEventListener('click', function() {
      const type = sellerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
      sellerPassword.setAttribute('type', type);

      const icon = this.querySelector('i');
      if(type === 'password') {
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
      } else {
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
      }
    });
  }

  // Auto-dismiss alerts
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.animation = 'slideDown 0.3s ease reverse';
      setTimeout(() => alert.remove(), 300);
    }, 4000);
  });
</script>

</body>
</html>