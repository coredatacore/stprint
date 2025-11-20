<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

$order = null;
$msg = '';
$search_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
    $search_id = trim($_POST['order_id'] ?? $_GET['id'] ?? '');
    
    if ($search_id) {
        try {
            $db = db_connect();
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $search_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
            } else {
                $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-circle-fill'></i> <span>Order not found!</span></div>";
            }
            
            $stmt->close();
            $db->close();
        } catch(Exception $e) {
            $msg = "<div class='alert alert-danger'><i class='bi bi-exclamation-circle-fill'></i> <span>Error: " . $e->getMessage() . "</span></div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill'></i> <span>Please enter an order ID</span></div>";
    }
}

// Status timeline
$status_flow = ['pending' => 'Pending', 'on_progress' => 'On Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
$status_order = ['pending' => 0, 'on_progress' => 1, 'completed' => 2, 'cancelled' => 3];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Track Order - ST PRINT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
      padding: 20px 0;
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
      margin-bottom: 40px;
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

    /* ====== MAIN CONTAINER ====== */
    .main-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* ====== PAGE TITLE ====== */
    .page-title {
      font-size: 2.2rem;
      font-weight: 900;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-align: center;
      margin-bottom: 15px;
    }

    .page-subtitle {
      text-align: center;
      color: #666;
      margin-bottom: 40px;
      font-size: 1.05rem;
    }

    /* ====== SEARCH CARD ====== */
    .search-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.1);
      margin-bottom: 40px;
      animation: slideUp 0.8s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .search-form {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .form-control {
      flex: 1;
      min-width: 250px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 14px 16px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .form-control::placeholder {
      color: #bbb;
    }

    .form-control:focus {
      border-color: #00C6FF;
      background: white;
      box-shadow: 0 0 0 0.3rem rgba(0, 198, 255, 0.15);
      outline: none;
    }

    .btn-search {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      padding: 14px 30px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.25);
      white-space: nowrap;
    }

    .btn-search:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 198, 255, 0.35);
      color: white;
    }

    /* ====== ALERTS ====== */
    .alert {
      border-radius: 12px;
      border: none;
      padding: 15px;
      margin-bottom: 25px;
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

    /* ====== ORDER CARD ====== */
    .order-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.1);
      margin-bottom: 40px;
      animation: slideUp 0.8s ease 0.2s both;
    }

    .order-header {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      color: white;
      padding: 25px;
      border-radius: 15px;
      margin: -40px -40px 30px -40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .order-id {
      font-size: 1.5rem;
      font-weight: 800;
    }

    .order-date {
      font-size: 0.95rem;
      opacity: 0.9;
    }

    /* ====== INFO GRID ====== */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-bottom: 35px;
    }

    .info-item {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 15px;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .info-item:hover {
      border-color: #00C6FF;
      background: rgba(0, 198, 255, 0.05);
    }

    .info-label {
      font-size: 0.85rem;
      color: #666;
      font-weight: 600;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .info-label i {
      color: #00C6FF;
      font-size: 1rem;
    }

    .info-value {
      font-size: 1.1rem;
      font-weight: 700;
      color: #2c2c2c;
    }

    /* ====== STATUS TIMELINE ====== */
    .timeline-section {
      margin-top: 40px;
      padding-top: 30px;
      border-top: 2px solid #e0e0e0;
    }

    .timeline-title {
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 30px;
      color: #2c2c2c;
    }

    .timeline {
      position: relative;
      padding: 0;
    }

    .timeline::before {
      content: '';
      position: absolute;
      left: 20px;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(180deg, #00C6FF 0%, #FF57B9 100%);
    }

    .timeline-item {
      margin-bottom: 30px;
      margin-left: 70px;
      position: relative;
    }

    .timeline-dot {
      position: absolute;
      left: -60px;
      top: 5px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      border: 3px solid #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 1.2rem;
      transition: all 0.3s ease;
    }

    .timeline-item.active .timeline-dot {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border-color: #00C6FF;
      color: white;
      transform: scale(1.15);
      box-shadow: 0 0 0 8px rgba(0, 198, 255, 0.1);
    }

    .timeline-item.completed .timeline-dot {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border-color: #10b981;
      color: white;
    }

    .timeline-item.cancelled .timeline-dot {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      border-color: #ef4444;
      color: white;
    }

    .timeline-content {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 12px;
      border-left: 4px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .timeline-item.active .timeline-content {
      background: rgba(0, 198, 255, 0.08);
      border-color: #00C6FF;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.1);
    }

    .timeline-item.completed .timeline-content {
      background: rgba(16, 185, 129, 0.08);
      border-color: #10b981;
    }

    .timeline-item.cancelled .timeline-content {
      background: rgba(239, 68, 68, 0.08);
      border-color: #ef4444;
    }

    .timeline-status {
      font-weight: 700;
      font-size: 1.05rem;
      margin-bottom: 5px;
      color: #2c2c2c;
    }

    .timeline-time {
      font-size: 0.85rem;
      color: #999;
    }

    /* ====== STATUS BADGE ====== */
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 16px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.95rem;
      color: white;
      margin-top: 15px;
    }

    .status-pending {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .status-on_progress {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .status-completed {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .status-cancelled {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    /* ====== BACK BUTTON ====== */
    .btn-back {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
      margin-top: 20px;
    }

    .btn-back:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.3);
      color: white;
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
      .page-title {
        font-size: 1.5rem;
      }

      .search-card, .order-card {
        padding: 25px;
      }

      .order-header {
        margin: -25px -25px 20px -25px;
        flex-direction: column;
        text-align: center;
      }

      .search-form {
        flex-direction: column;
      }

      .form-control, .btn-search {
        width: 100%;
      }

      .info-grid {
        grid-template-columns: 1fr;
      }

      .timeline::before {
        left: 15px;
      }

      .timeline-dot {
        left: -45px;
        width: 35px;
        height: 35px;
      }

      .timeline-item {
        margin-left: 55px;
      }
    }

    @media (max-width: 480px) {
      .main-container {
        padding: 0 15px;
      }

      .page-title {
        font-size: 1.3rem;
      }

      .order-header {
        padding: 15px;
      }

      .order-id {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-custom">
  <div class="container-sm d-flex align-items-center justify-content-between">
    <a class="navbar-brand-custom" href="index.php">
      <i class="bi bi-printer-fill"></i> ST PRINT
    </a>

    <div class="nav-buttons">
      <a href="index.php" class="nav-btn">
        <i class="bi bi-house-fill"></i> Home
      </a>
      <a href="seller_login.php" class="nav-btn">
        <i class="bi bi-shop"></i> Seller
      </a>
      <a href="admin_login.php" class="nav-btn">
        <i class="bi bi-shield-lock"></i> Admin
      </a>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="main-container">
  <!-- TITLE -->
  <h1 class="page-title">
    <i class="bi bi-search"></i> Track Your Order
  </h1>
  <p class="page-subtitle">Enter your order ID to see the latest status and updates</p>

  <!-- SEARCH CARD -->
  <div class="search-card">
    <form method="post" class="search-form">
      <input 
        type="number" 
        name="order_id" 
        class="form-control" 
        placeholder="Enter Order ID (e.g., 1)" 
        value="<?=htmlspecialchars($search_id)?>"
        required
      >
      <button type="submit" class="btn-search">
        <i class="bi bi-search"></i> Track Order
      </button>
    </form>
  </div>

  <!-- ALERTS -->
  <?= $msg ?>

  <!-- ORDER DETAILS -->
  <?php if($order): ?>
    <div class="order-card">
      <!-- ORDER HEADER -->
      <div class="order-header">
        <div>
          <div class="order-id">Order #<?= htmlspecialchars($order['id']) ?></div>
          <div class="order-date">
            <i class="bi bi-calendar3"></i> 
            <?= htmlspecialchars($order['created_at']) ?>
          </div>
        </div>
        <?php
          $s = $order['status'];
          $status_class = $s === 'pending' ? 'status-pending' :
                 ($s === 'on_progress' ? 'status-on_progress' :
                 ($s === 'completed' ? 'status-completed' : 'status-cancelled'));
          $status_icon = $s === 'pending' ? 'bi-clock' :
                 ($s === 'on_progress' ? 'bi-hourglass-split' :
                 ($s === 'completed' ? 'bi-check-circle' : 'bi-x-circle'));
          $status_text = $s === 'pending' ? 'Pending' :
                 ($s === 'on_progress' ? 'On Progress' :
                 ($s === 'completed' ? 'Completed' : 'Cancelled'));
        ?>
        <span class="status-badge <?= $status_class ?>">
          <i class="bi <?= $status_icon ?>"></i> <?= $status_text ?>
        </span>
      </div>

      <!-- INFO GRID -->
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-person-fill"></i> Customer Name
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['name']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-envelope-fill"></i> Email
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['email']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-building"></i> Department
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['department']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-calendar-event"></i> Year Level
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['year_level']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-printer-fill"></i> Print Type
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['print_type']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-palette-fill"></i> Design
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['design_option']) ?>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-stack"></i> Quantity
          </div>
          <div class="info-value">
            <?= htmlspecialchars($order['quantity']) ?> pcs
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">
            <i class="bi bi-cash-coin"></i> Total Amount
          </div>
          <div class="info-value">
            ₱<?= number_format($order['total_price'], 2) ?>
          </div>
        </div>

        <?php if($order['description']): ?>
          <div class="info-item" style="grid-column: 1 / -1;">
            <div class="info-label">
              <i class="bi bi-chat-left-text-fill"></i> Special Instructions
            </div>
            <div class="info-value">
              <?= htmlspecialchars($order['description']) ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- TIMELINE -->
      <div class="timeline-section">
        <h3 class="timeline-title">
          <i class="bi bi-hourglass-split"></i> Order Status Timeline
        </h3>

        <div class="timeline">
          <!-- Pending -->
          <div class="timeline-item <?= ($order['status'] === 'pending' ? 'active' : ($order['status'] !== 'cancelled' ? 'completed' : '')) ?>">
            <div class="timeline-dot">
              <i class="bi bi-clock"></i>
            </div>
            <div class="timeline-content">
              <div class="timeline-status">📋 Pending</div>
              <div class="timeline-time">Order received and waiting to be processed</div>
            </div>
          </div>

          <!-- On Progress -->
          <div class="timeline-item <?= ($order['status'] === 'on_progress' ? 'active' : ($order['status'] === 'completed' || $order['status'] === 'cancelled' ? 'completed' : '')) ?>">
            <div class="timeline-dot">
              <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="timeline-content">
              <div class="timeline-status">⚙️ On Progress</div>
              <div class="timeline-time">Your order is being prepared</div>
            </div>
          </div>

          <!-- Completed -->
          <div class="timeline-item <?= ($order['status'] === 'completed' ? 'active completed' : '') ?>">
            <div class="timeline-dot">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="timeline-content">
              <div class="timeline-status">✅ Completed</div>
              <div class="timeline-time">Your order is ready for pickup/delivery</div>
            </div>
          </div>

          <!-- Cancelled -->
          <div class="timeline-item <?= ($order['status'] === 'cancelled' ? 'active cancelled' : '') ?>">
            <div class="timeline-dot">
              <i class="bi bi-x-circle"></i>
            </div>
            <div class="timeline-content">
              <div class="timeline-status">❌ Cancelled</div>
              <div class="timeline-time">This order has been cancelled</div>
            </div>
          </div>
        </div>
      </div>

      <a href="track_order.php" class="btn-back">
        <i class="bi bi-arrow-left"></i> Track Another Order
      </a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>