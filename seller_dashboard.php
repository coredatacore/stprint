<?php
// seller_dashboard.php
session_start();
require_once 'config.php';
if (!isset($_SESSION['seller_logged_in']) || !$_SESSION['seller_logged_in']) { header('Location: seller_login.php'); exit; }
$seller_id = (int)$_SESSION['seller_id'];
$db = db_connect();

// inventory
$stmt = $db->prepare("SELECT * FROM seller_items WHERE seller_id=? ORDER BY created_at DESC");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$inv = $stmt->get_result();
$stmt->close();

// sales summary
$stmt2 = $db->prepare("SELECT SUM(total_price) AS total_sales, SUM(quantity) AS total_items FROM seller_sales WHERE seller_id=?");
$stmt2->bind_param('i', $seller_id);
$stmt2->execute();
$sumr = $stmt2->get_result()->fetch_assoc();
$total_sales = $sumr['total_sales'] ?? 0;
$total_items = $sumr['total_items'] ?? 0;
$stmt2->close();

// seller commission rate
$seller = $db->prepare("SELECT commission_rate, full_name FROM sellers WHERE id=?");
$seller->bind_param('i', $seller_id);
$seller->execute();
$seller->bind_result($commission_rate, $seller_name);
$seller->fetch();
$seller->close();

?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Seller Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<!-- HTML5 QR Code library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
</head><body>
<nav class="navbar"><div class="container-sm d-flex align-items-center"><a class="navbar-brand" href="index.php">ST PRINT</a>
<div class="ms-auto d-flex align-items-center" style="gap:10px;"><span class="text-muted-small">Hello, <?=htmlspecialchars($seller_name)?></span>
<a href="seller_change_password.php" class="btn btn-sm-custom">Change Password</a>
<a href="seller_logout.php" class="btn btn-sm-custom">Logout</a></div></div></nav>

<div class="container-sm my-4">
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card mb-3">
        <div style="padding:12px;">
          <h5>Sales Summary</h5>
          <p>Total items sold: <strong><?= (int)$total_items ?></strong></p>
          <p>Total sales: <strong>₱<?= number_format($total_sales,2) ?></strong></p>
          <p>Commission rate: <strong><?= number_format($commission_rate,2) ?>%</strong></p>
          <p>Earnings estimate: <strong>₱<?= number_format(($total_sales * $commission_rate / 100),2) ?></strong></p>
        </div>
      </div>

      <div class="card">
        <div style="padding:12px;">
          <h5>Scan Item (Camera)</h5>
          <div id="reader" style="width:100%"></div>
          <small class="text-muted-small">Allow camera access. Scans item code or QR and fetches the item.</small>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card mb-3">
        <div style="padding:12px;">
          <h5>Quick Lookup (Manual)</h5>
          <div class="d-flex" style="gap:8px;">
            <input id="manual_code" class="form-control" placeholder="Enter item code">
            <button id="lookup_btn" class="btn btn-primary">Lookup</button>
          </div>
          <div id="lookup_result" style="margin-top:12px; display:none;"></div>
        </div>
      </div>

      <div class="card">
        <div style="padding:12px;">
          <h5>Your Inventory</h5>
          <table class="table-modern">
            <thead><tr><th>Code</th><th>Product</th><th>Price</th><th>Qty</th><th>Sold</th><th>Action</th></tr></thead>
            <tbody>
            <?php while($row = $inv->fetch_assoc()): ?>
              <tr>
                <td><?=htmlspecialchars($row['item_code'])?></td>
                <td><?=htmlspecialchars($row['product_name'])?></td>
                <td>₱<?=number_format($row['price'],2)?></td>
                <td><?= (int)$row['quantity'] ?></td>
                <td><?= (int)$row['sold_count'] ?></td>
                <td>
                  <div style="display:flex; gap:8px;">
                    <button class="btn btn-sm-custom mark-sold" data-id="<?= $row['id'] ?>" data-price="<?= $row['price'] ?>">Mark Sold</button>
                    <button class="btn btn-sm-custom mark-return" data-id="<?= $row['id'] ?>">Mark Return</button>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="action_msg" style="margin-top:12px"></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const sellerId = <?= json_encode($seller_id) ?>;

// init camera scanner
const html5QrcodeScanner = new Html5Qrcode("reader");
function onScanSuccess(decodedText, decodedResult) {
  // stop scanner briefly
  html5QrcodeScanner.stop().then(()=>{}).catch(()=>{});
  lookupCode(decodedText);
}
function onScanError(err) { /* ignore */ }
Html5Qrcode.getCameras().then(devices=>{
  if(devices && devices.length) {
    const cameraId = devices[0].id;
    html5QrcodeScanner.start(cameraId, {fps:10, qrbox:250}, onScanSuccess, onScanError);
  }
}).catch(err=>{ console.log('No camera',err); });

// manual lookup
$('#lookup_btn').on('click', function(){
  const code = $('#manual_code').val().trim();
  if(!code) return alert('Enter code');
  lookupCode(code);
});

function lookupCode(code){
  $('#lookup_result').hide().html('');
  $.get('seller_scan_lookup.php', {code: code}, function(resp){
    if(!resp || resp.error){
      $('#lookup_result').html('<div class="alert alert-danger">Item not found.</div>').show();
      return;
    }
    const item = resp.item;
    let html = '<div><strong>'+item.product_name+'</strong> <br>Code: '+item.item_code+' <br>Price: ₱'+parseFloat(item.price).toFixed(2)+' <br>Qty: '+item.quantity+'</div>';
    html += '<div style="margin-top:8px;">' +
            '<button class="btn btn-primary" id="lr_sold" data-id="'+item.id+'" data-price="'+item.price+'">Mark Sold</button> '+
            '<button class="btn btn-sm-custom" id="lr_return" data-id="'+item.id+'">Mark Return</button></div>';
    $('#lookup_result').html(html).show();
  }, 'json').fail(()=>$('#lookup_result').html('<div class="alert alert-danger">Server error</div>').show());
}

// actions
$(document).on('click', '.mark-sold, #lr_sold', function(){
  const id = $(this).data('id'); const price = $(this).data('price') || 0;
  if(!confirm('Mark item as SOLD?')) return;
  $.post('seller_action.php', {action:'sold', item_id: id, price: price}, function(r){
    location.reload();
  }, 'json');
});
$(document).on('click', '.mark-return, #lr_return', function(){
  const id = $(this).data('id');
  if(!confirm('Mark item as RETURNED?')) return;
  $.post('seller_action.php', {action:'returned', item_id: id}, function(r){
    location.reload();
  }, 'json');
});
</script>
</body></html>
