<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ST PRINT - Order Now</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Theme -->
  <link href="style.css" rel="stylesheet">

  <style>
    :root {
      --primary-cyan: #00C6FF;
      --primary-pink: #FF57B9;
      --accent-purple: #667eea;
      --light-bg: #ffffff;
      --light-bg-secondary: #f8f9fa;
      --dark-bg: #0d1117;
      --dark-bg-secondary: #161b22;
      --text-light: #1a1a2e;
      --text-dark: #e8e8e8;
    }

    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      color: var(--text-light);
      transition: all 0.35s ease;
    }

    body.dark-mode {
      background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-bg-secondary) 100%);
      color: var(--text-dark);
    }

    /* ====== NAVBAR ====== */
    .navbar-index {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 3px solid var(--primary-cyan);
      padding: 15px 0;
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.15);
      transition: all 0.35s ease;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    body.dark-mode .navbar-index {
      background: rgba(13, 17, 23, 0.95);
      border-bottom-color: var(--primary-cyan);
    }

    .navbar-brand-index {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 900;
      font-size: 1.5rem;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .navbar-brand-index:hover {
      transform: scale(1.05);
    }

    .nav-links {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .nav-link-btn {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      color: white !important;
      padding: 10px 18px !important;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9rem;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
    }

    .nav-link-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.3);
    }

    .theme-toggle-btn {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      border: none;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 198, 255, 0.2);
    }

    .theme-toggle-btn:hover {
      transform: rotate(20deg) scale(1.1);
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.3);
    }

    /* ====== HERO SECTION ====== */
    .hero-section {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      color: white;
      padding: 80px 30px;
      border-radius: 20px;
      margin: 30px auto;
      max-width: 1200px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 198, 255, 0.25);
      animation: slideUp 0.8s ease;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      top: -100px;
      right: -100px;
      animation: float 6s ease-in-out infinite;
    }

    .hero-section::after {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 50%;
      bottom: -50px;
      left: -50px;
      animation: float 8s ease-in-out infinite reverse;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
    }

    .hero-content h1 {
      font-size: 3rem;
      font-weight: 900;
      margin-bottom: 15px;
      text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      animation: slideDown 0.8s ease 0.2s both;
    }

    .hero-content p {
      font-size: 1.2rem;
      opacity: 0.95;
      margin-bottom: 30px;
      animation: slideUp 0.8s ease 0.4s both;
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

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(20px); }
    }

    /* ====== ORDER FORM SECTION ====== */
    .form-section {
      max-width: 900px;
      margin: 40px auto;
      padding: 0 20px;
      animation: slideUp 0.8s ease 0.6s both;
    }

    .form-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 40px rgba(0, 198, 255, 0.15);
      transition: all 0.3s ease;
    }

    body.dark-mode .form-card {
      background: var(--dark-bg-secondary);
      color: var(--text-dark);
    }

    .form-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 60px rgba(0, 198, 255, 0.25);
    }

    .form-card h3 {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 800;
      margin-bottom: 30px;
      font-size: 1.8rem;
    }

    .form-group-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-label {
      font-weight: 700;
      margin-bottom: 10px;
      color: var(--text-light);
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
    }

    body.dark-mode .form-label {
      color: var(--text-dark);
    }

    .form-control, .form-select {
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
      color: var(--text-light);
    }

    body.dark-mode .form-control,
    body.dark-mode .form-select {
      background: #21262d;
      border-color: #30363d;
      color: var(--text-dark);
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary-cyan);
      box-shadow: 0 0 0 0.3rem rgba(0, 198, 255, 0.2);
      outline: none;
    }

    /* ====== PRICE DISPLAY ====== */
    .price-display {
      background: linear-gradient(135deg, rgba(0, 198, 255, 0.1) 0%, rgba(255, 87, 185, 0.1) 100%);
      border-left: 4px solid var(--primary-cyan);
      padding: 20px;
      border-radius: 12px;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    .price-display:hover {
      box-shadow: 0 4px 20px rgba(0, 198, 255, 0.15);
    }

    .price-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      font-size: 1.05rem;
    }

    .price-item strong {
      color: var(--primary-cyan);
    }

    .price-total {
      border-top: 2px solid var(--primary-cyan);
      padding-top: 12px;
      margin-top: 12px;
      font-size: 1.3rem;
      font-weight: 800;
      display: flex;
      justify-content: space-between;
      color: var(--primary-cyan);
    }

    /* ====== SUBMIT BUTTON ====== */
    .submit-btn {
      width: 100%;
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      border: none;
      color: white;
      padding: 16px;
      border-radius: 12px;
      font-size: 1.05rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
      box-shadow: 0 6px 20px rgba(0, 198, 255, 0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 198, 255, 0.35);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    /* ====== LOADING OVERLAY ====== */
    #loadingOverlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(5px);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      gap: 20px;
    }

    .spinner {
      width: 60px;
      height: 60px;
      border: 5px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* ====== SUCCESS MODAL ====== */
    #successModal {
      display: none;
      position: fixed;
      inset: 0;
      backdrop-filter: blur(8px);
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 10000;
    }

    .modal-box {
      background: white;
      padding: 40px;
      border-radius: 20px;
      width: 90%;
      max-width: 420px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: pop 0.35s ease;
    }

    body.dark-mode .modal-box {
      background: var(--dark-bg-secondary);
      color: var(--text-dark);
    }

    .modal-icon {
      font-size: 4rem;
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
    }

    .modal-box h4 {
      color: var(--text-light);
      font-weight: 800;
      margin-bottom: 15px;
    }

    body.dark-mode .modal-box h4 {
      color: var(--text-dark);
    }

    @keyframes pop {
      0% { transform: scale(0.7); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* ====== FOOTER ====== */
    footer {
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      color: white;
      text-align: center;
      padding: 30px;
      margin-top: 60px;
      font-weight: 600;
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
      .hero-section {
        padding: 50px 20px;
      }

      .hero-content h1 {
        font-size: 2rem;
      }

      .form-card {
        padding: 25px;
      }

      .form-group-row {
        grid-template-columns: 1fr;
      }
    }

    /* ====== PASSWORD WRAPPER ====== */
    .password-wrapper {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      background: linear-gradient(135deg, var(--primary-cyan) 0%, var(--primary-pink) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 1.1rem;
      user-select: none;
      z-index: 10;
      transition: all 0.2s ease;
    }

    .password-toggle:hover {
      transform: translateY(-50%) scale(1.2);
    }

    .form-control.has-password {
      padding-right: 40px;
    }
  </style>
</head>

<body>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay">
  <div class="spinner"></div>
  <p style="color: white; font-size: 1.1rem;">Processing your order...</p>
</div>

<!-- SUCCESS MODAL -->
<div id="successModal">
  <div class="modal-box">
    <div class="modal-icon">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <h4>Order Submitted! 🎉</h4>
    <p id="modalText" style="color: #666; margin-bottom: 20px;"></p>
    <button class="submit-btn" onclick="closeModal()">
      <i class="bi bi-check"></i> Got It!
    </button>
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar-index">
  <div class="container-sm d-flex align-items-center justify-content-between">
    <a class="navbar-brand-index" href="index.php">
      <i class="bi bi-printer-fill"></i> ST PRINT
    </a>

    <div class="nav-links">
      <button class="theme-toggle-btn" id="themeToggle" title="Toggle Dark Mode">
        <i class="bi bi-moon-stars"></i>
      </button>
      <a href="track_order.php" class="nav-link-btn">
        <i class="bi bi-search"></i> Track Order
      </a>
      <a href="seller_login.php" class="nav-link-btn">
        <i class="bi bi-shop"></i> Seller
      </a>
      <a href="admin_login.php" class="nav-link-btn">
        <i class="bi bi-shield-lock"></i> Admin
      </a>
    </div>
  </div>
</nav>

<!-- HERO SECTION -->
<div class="hero-section">
  <div class="hero-content">
    <h1>Order Your CCS Panther Collectibles</h1>
  </div>
</div>

<!-- ORDER FORM -->
<div class="form-section">
  <div class="form-card">
    <h3><i class="bi bi-bag-check"></i> Place Your Order</h3>

    <form id="orderForm">
      <div class="form-group-row">
        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-person"></i> Full Name
          </label>
          <input name="name" class="form-control" placeholder="Juan Dela Cruz" required>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-envelope"></i> Email
          </label>
          <input name="email" type="email" class="form-control" placeholder="your@email.com" required>
        </div>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-building"></i> Department
          </label>
          <select name="department" class="form-select" required>
            <option value="">Select Department</option>
            <option>BSIS</option>
            <option>BSIT</option>
            <option>BSCS</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-calendar"></i> Year Level
          </label>
          <select name="year_level" class="form-select" required>
            <option value="">Select Year</option>
            <option>1st Year</option>
            <option>2nd Year</option>
            <option>3rd Year</option>
            <option>4th Year</option>
            <option>Graduate</option>
            <option>Faculty</option>
          </select>
        </div>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-printer"></i> Print Type
          </label>
          <select id="print_type" name="print_type" class="form-select" required>
            <option value="">Select Type</option>
            <option data-price="45">Button Pin</option>
            <option data-price="55">Mirror Badge</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-palette"></i> Design Option
          </label>
          <select name="design_option" class="form-select" required>
            <option value="">Select Design</option>
            <option>Panthers</option>
            <option>ISS</option>
            <option>Arayyy kooo</option>
            <option>BSISnganiiii</option>
            <option>Analyze</option>
            <option>BSCSnganiii</option>
            <option>BSITnganiii</option>
          </select>
        </div>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-cash-coin"></i> Unit Price
          </label>
          <input id="price" class="form-control" readonly placeholder="₱0.00">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="bi bi-box"></i> Quantity
          </label>
          <input id="quantity" name="quantity" type="number" min="1" value="1" class="form-control" required>
        </div>
      </div>

      <div class="price-display">
        <div class="price-item">
          <span>Unit Price:</span>
          <strong id="displayPrice">₱0.00</strong>
        </div>
        <div class="price-item">
          <span>Quantity:</span>
          <strong id="displayQty">1</strong>
        </div>
        <div class="price-total">
          <span>Total Amount:</span>
          <strong id="displayTotal">₱0.00</strong>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="bi bi-chat-left-text"></i> Special Instructions
        </label>
        <textarea name="description" class="form-control" rows="3" placeholder="Add any special requests..."></textarea>
      </div>

      <button type="submit" class="submit-btn">
        <i class="bi bi-send-fill"></i> Submit Order
      </button>
    </form>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <small>&copy; <?php echo date('Y'); ?> ST PRINT. All rights reserved.</small>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  // ====== DARK MODE ======
  const themeToggle = document.getElementById('themeToggle');
  const body = document.body;

  if (localStorage.getItem('dark-mode') === 'true') {
    body.classList.add('dark-mode');
    updateThemeIcon();
  }

  themeToggle.addEventListener('click', function() {
    body.classList.toggle('dark-mode');
    localStorage.setItem('dark-mode', body.classList.contains('dark-mode'));
    updateThemeIcon();
  });

  function updateThemeIcon() {
    const icon = themeToggle.querySelector('i');
    if (body.classList.contains('dark-mode')) {
      icon.classList.remove('bi-moon-stars');
      icon.classList.add('bi-sun-fill');
    } else {
      icon.classList.remove('bi-sun-fill');
      icon.classList.add('bi-moon-stars');
    }
  }

  // ====== PRICE CALCULATOR ======
  const prices = {
    "Button Pin": 45,
    "Mirror Badge": 55
  };

  $('#print_type').on('change', function() {
    const selectedText = $(this).find('option:selected').text();
    const price = prices[selectedText] || 0;
    
    $('#price').val(price ? '₱' + price : '');
    $('#displayPrice').text(price ? '₱' + price : '₱0.00');
    calculateTotal();
  });

  $('#quantity').on('input', function() {
    $('#displayQty').text($(this).val());
    calculateTotal();
  });

  function calculateTotal() {
    const price = parseFloat($('#price').val().replace('₱', '')) || 0;
    const qty = parseInt($('#quantity').val()) || 0;
    const total = price * qty;
    
    $('#displayTotal').text(total > 0 ? '₱' + total.toFixed(2) : '₱0.00');
  }

  // ====== FORM SUBMISSION ======
  $('#orderForm').on('submit', function(e) {
    e.preventDefault();

    const total = $('#displayTotal').text().replace('₱', '');
    const price = $('#displayPrice').text().replace('₱', '');

    if (!$('#print_type').val()) {
      alert('⚠️ Please select a print type');
      return;
    }

    if (parseFloat(total) === 0) {
      alert('⚠️ Price is ₱0. Please select a valid item');
      return;
    }

    $("#loadingOverlay").css("display", "flex");

    const formData = $(this).serialize() + '&price=' + price + '&total_price=' + total;

    $.ajax({
      url: "submit_order.php",
      type: "POST",
      data: formData,
      dataType: "json",

      success: function(resp) {
        $("#loadingOverlay").hide();

        if (resp.status === "success") {
          $("#modalText").html(
            `<strong>Order ID: #${resp.order_id}</strong><br>` +
            `<small style="color: #999; margin-top: 10px; display: block;">Your order has been received! You will receive updates soon.</small>`
          );
          $("#successModal").css("display", "flex");

          setTimeout(() => {
            window.location.href = "thank_you.php";
          }, 2500);
        } else {
          alert('❌ ' + (resp.message || 'Error submitting order'));
        }
      },

      error: function(xhr, status, error) {
        $("#loadingOverlay").hide();
        console.error('Error:', error);
        alert('❌ Error submitting order. Please try again.');
      }
    });
  });

  function closeModal() {
    $("#successModal").hide();
    window.location.href = "thank_you.php";
  }

  // ====== SMOOTH SCROLL ======
  $('a[href^="#"]').on('click', function(e) {
    e.preventDefault();
    const target = $(this.getAttribute('href'));
    if (target.length) {
      $('html, body').stop().animate({
        scrollTop: target.offset().top - 100
      }, 1000);
    }
  });

  // ====== AUTO-DETECT SYSTEM PREFERENCE ======
  if (window.matchMedia && !localStorage.getItem('dark-mode')) {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      body.classList.add('dark-mode');
      updateThemeIcon();
    }
  }
</script>

</body>
</html>
