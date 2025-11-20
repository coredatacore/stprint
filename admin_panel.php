<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

$db = db_connect();

function _gv($k, $default = '') {
    return isset($_REQUEST[$k]) ? trim((string)$_REQUEST[$k]) : $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_status'
        && isset($_POST['order_id'], $_POST['status'])) {

        $oid = (int) $_POST['order_id'];
        $st = trim($_POST['status']);
        $allowed = ['pending','on_progress','completed','cancelled'];
        if (!in_array($st, $allowed, true)) $st = 'pending';

        $update = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($update) {
            $update->bind_param('si', $st, $oid);
            $update->execute();
            $update->close();
        }

        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'export') {
        $status = _gv('filter_status', '');
        $search = _gv('search', '');

        $sql = "SELECT id,name,email,department,year_level,print_type,price,quantity,total_price,status,created_at
                FROM orders WHERE 1=1";
        $params = [];
        $types = '';

        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status; $types .= 's';
        }
        if ($search !== '') {
            $sql .= " AND (name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%') OR print_type LIKE CONCAT('%',?,'%'))";
            $params[] = $search; $params[] = $search; $params[] = $search;
            $types .= 'sss';
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        if ($stmt) {
            if ($params) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $db->query($sql);
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_'.date('Ymd_His').'.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Name','Email','Dept','Year','Item','Price','Qty','Total','Status','Created']);
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                fputcsv($out, [
                    $r['id'],
                    $r['name'],
                    $r['email'],
                    $r['department'],
                    $r['year_level'],
                    $r['print_type'],
                    $r['price'],
                    $r['quantity'],
                    $r['total_price'],
                    $r['status'],
                    $r['created_at'],
                ]);
            }
            if ($stmt) $stmt->close();
        }
        fclose($out);
        exit;
    }
}

$filter_status = _gv('status', '');
$search = _gv('search', '');
$page = max(1, (int) (_gv('page', 1)));
$limit = 20;
$offset = ($page - 1) * $limit;

$base_sql = "FROM orders WHERE 1=1";
$params = [];
$types = '';

if ($filter_status !== '') {
    $base_sql .= " AND status = ?";
    $params[] = $filter_status; $types .= 's';
}
if ($search !== '') {
    $base_sql .= " AND (name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%') OR print_type LIKE CONCAT('%',?,'%'))";
    $params[] = $search; $params[] = $search; $params[] = $search; $types .= 'sss';
}

$count_sql = "SELECT COUNT(*) AS total " . $base_sql;
$count_stmt = $db->prepare($count_sql);
if ($count_stmt) {
    if ($params) $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $countRes = $count_stmt->get_result();
    $total = (int) ($countRes->fetch_assoc()['total'] ?? 0);
    $count_stmt->close();
} else {
    $tmpRes = $db->query($count_sql);
    $total = $tmpRes ? (int)($tmpRes->fetch_assoc()['total'] ?? 0) : 0;
}

$data_sql = "SELECT id,name,email,department,year_level,print_type,price,quantity,total_price,status,created_at " . $base_sql .
            " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params_with_limit = $params;
$types_with_limit = $types . 'ii';
$params_with_limit[] = $limit;
$params_with_limit[] = $offset;

$data_stmt = $db->prepare($data_sql);
if ($data_stmt) {
    $data_stmt->bind_param($types_with_limit, ...$params_with_limit);
    $data_stmt->execute();
    $res = $data_stmt->get_result();
} else {
    $res = $db->query("SELECT id,name,email,department,year_level,print_type,price,quantity,total_price,status,created_at " . $base_sql . " ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}");
}

$pages = $limit > 0 ? (int) ceil($total / $limit) : 1;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Panel - ST PRINT</title>
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

    /* ====== MAIN CONTAINER ====== */
    .main-container {
      max-width: 1400px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .page-title {
      font-size: 2rem;
      font-weight: 800;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* ====== CARDS ====== */
    .card-custom {
      background: white;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.1);
      margin-bottom: 25px;
      transition: all 0.3s ease;
      border: none;
    }

    .card-custom:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(0, 198, 255, 0.15);
    }

    /* ====== FILTER SECTION ====== */
    .filter-section {
      display: flex;
      gap: 12px;
      margin-bottom: 25px;
      flex-wrap: wrap;
      align-items: center;
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

    .btn-filter {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
    }

    .btn-filter:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.3);
      color: white;
    }

    /* ====== TABLE ====== */
    .table-container {
      overflow-x: auto;
    }

    .table-modern {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }

    .table-modern thead {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      color: white;
    }

    .table-modern th {
      padding: 18px;
      font-weight: 700;
      text-align: left;
      border: none;
    }

    .table-modern td {
      padding: 18px;
      border-bottom: 1px solid #e0e0e0;
      color: #2c2c2c;
    }

    .table-modern tbody tr:hover {
      background-color: rgba(0, 198, 255, 0.08);
    }

    .table-modern tbody tr:last-child td {
      border-bottom: none;
    }

    /* ====== STATUS STYLES ====== */
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.85rem;
      color: white;
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

    /* ====== ACTION SECTION ====== */
    .action-section {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .status-select {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.9rem;
      background: white;
      color: #2c2c2c;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .status-select:focus {
      border-color: #00C6FF;
      outline: none;
      box-shadow: 0 0 0 0.2rem rgba(0, 198, 255, 0.15);
    }

    .status-select option {
      padding: 8px;
      color: #2c2c2c;
    }

    .btn-update {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
    }

    .btn-update:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0, 198, 255, 0.3);
      color: white;
    }

    /* ====== EXPORT BUTTON ====== */
    .btn-export {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      color: white;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
      margin-bottom: 20px;
    }

    .btn-export:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
      color: white;
    }

    /* ====== PAGINATION ====== */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 30px;
    }

    .page-link {
      padding: 10px 14px;
      border-radius: 8px;
      border: 2px solid #e0e0e0;
      background: white;
      color: #00C6FF;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .page-link:hover {
      border-color: #00C6FF;
      background: #f8f9fa;
    }

    .page-link.active {
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border-color: #00C6FF;
      color: white;
    }

    /* ====== EMPTY STATE ====== */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #999;
    }

    .empty-state i {
      font-size: 4rem;
      color: #ddd;
      display: block;
      margin-bottom: 20px;
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
      .page-title {
        font-size: 1.5rem;
      }

      .filter-section {
        flex-direction: column;
      }

      .filter-section input,
      .filter-section select,
      .filter-section button {
        width: 100%;
      }

      .table-modern {
        font-size: 0.85rem;
      }

      .table-modern th,
      .table-modern td {
        padding: 12px;
      }

      .action-section {
        flex-direction: column;
        width: 100%;
      }

      .status-select,
      .btn-update {
        width: 100%;
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
      <span style="color: #2c2c2c; font-weight: 600; margin-right: 15px;">
        👋 Hello, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
      </span>

      <a href="admin_sellers.php" class="nav-btn">
        <i class="bi bi-people"></i> Sellers
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
    <i class="bi bi-box-seam"></i> Orders Management
  </h1>

  <!-- FILTER SECTION -->
  <div class="card-custom">
    <form method="get" class="filter-section">
      <input name="search" placeholder="🔍 Search by name, email, or item" value="<?=htmlspecialchars($search)?>" class="form-control" style="flex:1; min-width:200px;">
      <select name="status" class="form-select" style="width:160px;">
        <option value="">📋 All Status</option>
        <option value="pending" <?= $filter_status==='pending' ? 'selected' : '' ?>>⏳ Pending</option>
        <option value="on_progress" <?= $filter_status==='on_progress' ? 'selected' : '' ?>>⚙️ On Progress</option>
        <option value="completed" <?= $filter_status==='completed' ? 'selected' : '' ?>>✅ Completed</option>
        <option value="cancelled" <?= $filter_status==='cancelled' ? 'selected' : '' ?>>❌ Cancelled</option>
      </select>
      <button type="submit" class="btn-filter">
        <i class="bi bi-funnel"></i> Filter
      </button>
    </form>
  </div>

  <!-- EXPORT BUTTON -->
  <form method="post" style="display:inline-block;">
    <input type="hidden" name="action" value="export">
    <input type="hidden" name="filter_status" value="<?=htmlspecialchars($filter_status)?>">
    <input type="hidden" name="search" value="<?=htmlspecialchars($search)?>">
    <button type="submit" class="btn-export">
      <i class="bi bi-download"></i> Export CSV
    </button>
  </form>

  <!-- TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-modern">
        <thead>
          <tr>
            <th>ID</th>
            <th>Date & Time</th>
            <th>Customer</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
<?php if (isset($res) && $res && $res->num_rows > 0): ?>
  <?php while($row = $res->fetch_assoc()): ?>
          <tr>
            <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
            <td>
              <small><?= htmlspecialchars($row['created_at']) ?></small>
            </td>
            <td>
              <strong><?= htmlspecialchars($row['name']) ?></strong>
              <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                <?= htmlspecialchars($row['department']) ?> • <?= htmlspecialchars($row['year_level']) ?>
              </div>
            </td>
            <td>
              <strong><?= htmlspecialchars($row['print_type']) ?></strong>
              <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                Unit: ₱<?= number_format($row['price'],2) ?>
              </div>
            </td>
            <td>
              <strong><?= htmlspecialchars($row['quantity']) ?></strong>
            </td>
            <td>
              <strong style="color: #00C6FF; font-size: 1.05rem;">₱<?= number_format($row['total_price'],2) ?></strong>
            </td>
            <td>
<?php
    $s = $row['status'];
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
            </td>
            <td>
              <form method="post" class="action-section" style="margin: 0;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']) ?>">
                <select name="status" class="status-select">
                  <option value="pending" <?= $s=='pending'?'selected':'' ?>>Pending</option>
                  <option value="on_progress" <?= $s=='on_progress'?'selected':'' ?>>On Progress</option>
                  <option value="completed" <?= $s=='completed'?'selected':'' ?>>Completed</option>
                  <option value="cancelled" <?= $s=='cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn-update">
                  <i class="bi bi-check-lg"></i> Update
                </button>
              </form>
            </td>
          </tr>
  <?php endwhile; ?>
<?php else: ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <strong>No orders found</strong>
              </div>
            </td>
          </tr>
<?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <?php if($pages > 1): ?>
    <div class="pagination">
      <?php for($p=1;$p<=$pages;$p++): ?>
        <a class="page-link <?= $p==$page ? 'active' : '' ?>" 
           href="?page=<?=$p?>&status=<?=urlencode($filter_status)?>&search=<?=urlencode($search)?>">
          <?=$p?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>