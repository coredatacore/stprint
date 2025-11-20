<?php
session_start();
require_once 'config.php';
if(!isset($_SESSION['admin_logged_in'])){ header('Location: admin_login.php'); exit; }
$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $old = $_POST['old_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';
  if($new !== $confirm) $msg = '<div class="alert alert-danger">New passwords do not match.</div>';
  else{
    try{
      $db = db_connect();
      $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id=?");
      $stmt->bind_param('i', $_SESSION['admin_id']); $stmt->execute(); $stmt->bind_result($hash); $stmt->fetch(); $stmt->close();
      if(!password_verify($old,$hash)) $msg = '<div class="alert alert-danger">Old password incorrect.</div>';
      else{
        $nh = password_hash($new, PASSWORD_DEFAULT);
        $u = $db->prepare("UPDATE admin_users SET password_hash=? WHERE id=?");
        $u->bind_param('si', $nh, $_SESSION['admin_id']); $u->execute(); $u->close();
        $msg = '<div class="alert alert-success">Password updated.</div>';
      }
    }catch(Exception $e){ $msg = '<div class="alert alert-danger">Server error.</div>'; }
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Change Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar"><div class="container-sm d-flex align-items-center"><a class="navbar-brand" href="admin_panel.php">ST PRINT Admin</a></div></nav>
<div class="container-sm my-4">
  <div class="card mx-auto" style="max-width:520px;">
    <div style="padding:14px;">
      <h4>Change Password</h4>
      <?= $msg ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">Old password</label><input type="password" name="old_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">New password</label><input type="password" name="new_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Confirm new</label><input type="password" name="confirm_password" class="form-control" required></div>
        <div class="d-flex justify-content-between"><a href="admin_panel.php" class="btn btn-sm-custom">Back</a><button class="btn btn-primary">Update</button></div>
      </form>
    </div>
  </div>
</div>
</body></html>