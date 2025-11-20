<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['seller_logged_in']) || !$_SESSION['seller_logged_in']){
    header("Location: seller_login.php");
    exit;
}

$seller_id = $_SESSION['seller_id'];
$db = db_connect();

// Update status (optional)
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['order_id'], $_POST['status'])){
    $oid = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $allowed = ['pending','sold','returned','cancelled'];
    if(!in_array($status, $allowed)) $status = 'pending';

    // ensure seller owns this order
    $check = $db->prepare("SELECT id FROM orders WHERE id=? AND seller_id=?");
    $check->bind_param("ii", $oid, $seller_id);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $u = $db->prepare("UPDATE orders SET status=? WHERE id=?");
        $u->bind_param("si", $status, $oid);
        $u->execute();
        $u->close();
    }

    $check->close();
}

// Filters + Pagination
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM orders WHERE seller_id=?";
$params = [$seller_id];
$types = "i";

if($search !== ""){
    $sql .= " AND (name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%'))";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if($status_filter !== ""){
    $sql .= " AND status=?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// total pages
$totalRes = $db->query("SELECT FOUND_ROWS() AS total");
$total = (int)$totalRes->fetch_assoc()['total'];
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Seller Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<?php include "seller_navbar.php"; ?>

<div class="container-sm my-4">
    <h3>My Orders</h3>

    <form method="get" class="d-flex mb-3" style="gap:8px;">
        <input type="text" name="search" class="form-control" placeholder="Search name/email" value="<?=htmlspecialchars($search)?>">
        <select name="status" class="form-select" style="max-width:180px;">
            <option value="">All</option>
            <option value="pending"  <?=$status_filter==='pending'?'selected':''?>>Pending</option>
            <option value="sold"     <?=$status_filter==='sold'?'selected':''?>>Sold</option>
            <option value="returned" <?=$status_filter==='returned'?'selected':''?>>Returned</option>
            <option value="cancelled"<?=$status_filter==='cancelled'?'selected':''?>>Cancelled</option>
        </select>
        <button class="btn btn-primary">Filter</button>
    </form>

    <div class="card">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>When</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>

            <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?=$row['id']?></td>
                    <td><?=$row['created_at']?></td>
                    <td>
                        <?=htmlspecialchars($row['name'])?><br>
                        <small><?=htmlspecialchars($row['department'])?> • <?=htmlspecialchars($row['year_level'])?></small>
                    </td>
                    <td><?=$row['print_type']?></td>
                    <td><?=$row['quantity']?></td>
                    <td>₱<?=number_format($row['total_price'],2)?></td>

                    <td>
                        <span class="badge <?= 
                            $row['status']=='pending'?'bg-warning text-dark':
                            ($row['status']=='sold'?'bg-success':
                            ($row['status']=='returned'?'bg-secondary':'bg-danger'))
                        ?>">
                        <?=ucfirst($row['status'])?>
                        </span>
                    </td>

                    <td>
                        <form method="post" class="d-flex" style="gap:6px;">
                            <input type="hidden" name="order_id" value="<?=$row['id']?>">
                            <select name="status" class="form-select">
                                <option value="pending" <?=$row['status']=='pending'?'selected':''?>>Pending</option>
                                <option value="sold" <?=$row['status']=='sold'?'selected':''?>>Sold</option>
                                <option value="returned" <?=$row['status']=='returned'?'selected':''?>>Returned</option>
                                <option value="cancelled" <?=$row['status']=='cancelled'?'selected':''?>>Cancelled</option>
                            </select>
                            <button class="btn btn-sm-custom">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile;?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center p-3">
            <nav>
                <ul class="pagination">
                    <?php for($p=1;$p<=$pages;$p++): ?>
                    <li class="page-item <?= $p==$page?'active':'' ?>">
                        <a class="page-link" href="?page=<?=$p?>&search=<?=htmlspecialchars($search)?>&status=<?=htmlspecialchars($status_filter)?>">
                            <?=$p?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

</body>
</html>
