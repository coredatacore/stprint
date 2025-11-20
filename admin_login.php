<?php
session_start();
require_once 'config.php';
$err = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';
  if($u==='' || $p===''){ $err='Fill both fields'; }
  else {
    try{
      $db = db_connect();
      $stmt = $db->prepare("SELECT id,password_hash,fullname FROM admin_users WHERE username=? LIMIT 1");
      $stmt->bind_param('s',$u);
      $stmt->execute();
      $stmt->store_result();
      if($stmt->num_rows===1){
        $stmt->bind_result($id,$hash,$fullname);
        $stmt->fetch();
        if(password_verify($p,$hash)){
          $_SESSION['admin_logged_in']=true;
          $_SESSION['admin_id']=$id;
          $_SESSION['admin_name']=$fullname?:$u;
          header('Location: admin_panel.php'); exit;
        } else $err='Invalid credentials';
      } else $err='Invalid credentials';
      $stmt->close(); $db->close();
    }catch(Exception $e){ 
      error_log("Admin login error: " . $e->getMessage());
      $err='Server error'; 
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - ST PRINT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 50%, #5b7cfa 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Inter", "Segoe UI", sans-serif;
      position: relative;
      overflow: hidden;
    }

    /* Animated background elements */
    .bg-shape-1 {
      position: fixed;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      top: -100px;
      left: -100px;
      z-index: 0;
    }

    .bg-shape-2 {
      position: fixed;
      width: 400px;
      height: 400px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 50%;
      bottom: -150px;
      right: -150px;
      z-index: 0;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      padding: 20px;
      position: relative;
      z-index: 10;
    }

    .login-card {
      background: white;
      border-radius: 30px;
      padding: 50px 30px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Icon Container */
    .icon-container {
      width: 80px;
      height: 80px;
      margin: 0 auto 25px;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      color: white;
      box-shadow: 0 10px 30px rgba(0, 198, 255, 0.3);
      animation: pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes pop {
      0% {
        transform: scale(0);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    /* Title */
    .login-title {
      font-size: 1.8rem;
      font-weight: 800;
      color: #2c2c2c;
      text-align: center;
      margin-bottom: 10px;
    }

    .login-subtitle {
      font-size: 0.95rem;
      color: #999;
      text-align: center;
      margin-bottom: 30px;
    }

    /* Form Elements */
    .form-group {
      margin-bottom: 22px;
      animation: fadeIn 0.6s ease;
    }

    .form-group:nth-child(1) { animation-delay: 0.2s; }
    .form-group:nth-child(2) { animation-delay: 0.3s; }
    .form-group:nth-child(3) { animation-delay: 0.4s; }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-label {
      font-weight: 700;
      color: #2c2c2c;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
    }

    .form-label i {
      color: #00C6FF;
      font-size: 1.1rem;
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 14px 16px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #2c2c2c;
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

    /* Error Alert */
    .alert-error {
      background: rgba(239, 68, 68, 0.1);
      border: 2px solid #ef4444;
      border-radius: 12px;
      padding: 14px;
      margin-bottom: 20px;
      color: #ef4444;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
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

    /* Button Container */
    .button-group {
      display: flex;
      gap: 12px;
      margin-top: 28px;
    }

    /* Sign In Button */
    .btn-signin {
      flex: 1;
      background: linear-gradient(135deg, #00C6FF 0%, #FF57B9 100%);
      border: none;
      color: white;
      font-weight: 700;
      padding: 14px;
      border-radius: 12px;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 8px 20px rgba(0, 198, 255, 0.3);
      animation: fadeIn 0.6s ease 0.5s both;
    }

    .btn-signin:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0, 198, 255, 0.4);
    }

    .btn-signin:active {
      transform: translateY(-1px);
    }

    /* Back Button */
    .btn-back {
      flex: 1;
      background: white;
      border: 2px solid #e0e0e0;
      color: #2c2c2c;
      font-weight: 700;
      padding: 12px;
      border-radius: 12px;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      animation: fadeIn 0.6s ease 0.5s both;
    }

    .btn-back:hover {
      background: #f8f9fa;
      border-color: #00C6FF;
      color: #00C6FF;
      transform: translateY(-3px);
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-card {
        padding: 40px 25px;
        border-radius: 25px;
      }

      .login-title {
        font-size: 1.5rem;
      }

      .icon-container {
        width: 70px;
        height: 70px;
        font-size: 2rem;
      }

      .button-group {
        flex-direction: column;
      }

      .btn-signin,
      .btn-back {
        width: 100%;
      }
    }

    .container-sm {
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }
  </style>
</head>
<body>

  <div class="bg-shape-1"></div>
  <div class="bg-shape-2"></div>

  <div class="login-container">
    <div class="login-card">
      <!-- Icon -->
      <div class="icon-container">
        <i class="bi bi-shield-lock"></i>
      </div>

      <!-- Title -->
      <h2 class="login-title">Admin Sign In</h2>
      <p class="login-subtitle">Enter your admin credentials</p>

      <!-- Error Message -->
      <?php if($err): ?>
        <div class="alert-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <span><?=htmlspecialchars($err)?></span>
        </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="post">
        <!-- Username Field -->
        <div class="form-group">
          <label class="form-label" for="username">
            <i class="bi bi-person-fill"></i> Username
          </label>
          <input 
            type="text" 
            id="username"
            name="username" 
            class="form-control" 
            placeholder="Enter username"
            autocomplete="username"
            required
          >
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label class="form-label" for="password">
            <i class="bi bi-lock-fill"></i> Password
          </label>
          <div class="password-wrapper">
            <input 
              type="password" 
              id="password"
              name="password" 
              class="form-control has-password" 
              placeholder="Enter password"
              autocomplete="current-password"
              required
            >
            <span class="password-toggle" id="togglePassword">
              <i class="bi bi-eye-fill"></i>
            </span>
          </div>
        </div>

        <!-- Buttons -->
        <div class="button-group">
          <button type="submit" class="btn-signin">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
          </button>
          <a href="index.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Password Toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);

      const icon = this.querySelector('i');
      if(type === 'password') {
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
      } else {
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
      }
    });

    // Auto focus username
    document.getElementById('username').focus();
  </script>

</body>
</html>