<?php
session_start();
require_once 'config.php';
if(!isset($_SESSION['seller_logged_in'])){ header('Location: seller_login.php'); exit; }
$seller_id = (int)$_SESSION['seller_id']; $msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $old = $_POST['old_password'] ?? ''; $new = $_POST['new_password'] ?? ''; $confirm = $_POST['confirm_password'] ?? '';
    if($new !== $confirm) $msg = '<div class="alert alert-danger">New passwords do not match.</div>';
    else {
        $db = db_connect();
        $stmt = $db->prepare("SELECT password_hash FROM sellers WHERE id=?");
        $stmt->bind_param('i',$seller_id); $stmt->execute(); $stmt->bind_result($hash); $stmt->fetch(); $stmt->close();
        if(!password_verify($old,$hash)) $msg = '<div class="alert alert-danger">Old password incorrect.</div>';
        else {
            $nh = password_hash($new,PASSWORD_DEFAULT);
            $u = $db->prepare("UPDATE sellers SET password_hash=? WHERE id=?"); $u->bind_param('si',$nh,$seller_id); $u->execute(); $u->close();
            $msg = '<div class="alert alert-success">Password updated.</div>';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Seller Change Password</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link href="style.css" rel="stylesheet"></head>
<body>
<nav class="navbar"><div class="container-sm d-flex align-items-center"><a class="navbar-brand" href="seller_dashboard.php">ST PRINT Seller</a></div></nav>
<div class="container-sm my-4">
  <div class="card mx-auto" style="max-width:520px;">
    <div style="padding:14px;">
      <h4>Change Password</h4>
      <?= $msg ?>
      <form method="post">
        <div class="mb-2"><label>Old password</label><input name="old_password" type="password" class="form-control" required></div>
        <div class="mb-2"><label>New password</label><input name="new_password" type="password" class="form-control" required></div>
        <div class="mb-2"><label>Confirm</label><input name="confirm_password" type="password" class="form-control" required></div>
        <div class="d-flex justify-content-between"><a href="seller_dashboard.php" class="btn btn-sm-custom">Back</a><button class="btn btn-primary">Update</button></div>
      </form>
    </div>
  </div>
</div>
</body></html>