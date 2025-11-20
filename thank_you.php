<?php ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Thank You - ST PRINT</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Canvas Confetti JS -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <style>
    body {
      background:#f5f5f5;
      font-family: "Poppins", sans-serif;
      text-align:center;
      padding:40px 20px;
      animation:fade .5s ease;
    }

    @keyframes fade {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }

    .box {
      background:white;
      padding:35px;
      border-radius:18px;
      max-width:430px;
      margin:auto;
      box-shadow:0 6px 20px rgba(0,0,0,0.08);
    }

    .check-icon {
      font-size:70px;
      color:#28a745;
      margin-bottom:10px;
      animation:pop .4s ease;
    }

    @keyframes pop {
      0% { transform:scale(0.4); opacity:0; }
      100% { transform:scale(1); opacity:1; }
    }

    .btn-modern {
      background: linear-gradient(45deg, #00C6FF, #FF57B9);
      color:white;
      padding:10px 20px;
      border-radius:8px;
      text-decoration:none;
      display:inline-block;
      margin-top:15px;
      transition:.3s ease;
    }

    .btn-modern:hover {
      transform:scale(1.04);
      color:white;
      text-decoration:none;
    }

    body.dark-mode {
      background:#121212;
      color:white;
    }

    body.dark-mode .box {
      background:#1f1f1f;
      color:white;
      box-shadow:0 6px 20px rgba(255,255,255,0.08);
    }
  </style>

</head>
<body>

<div class="box">
  <i class="bi bi-check-circle-fill check-icon"></i>
  <h2>Thank You!</h2>
  <p>Your order has been submitted successfully.</p>
  <p class="text-muted">You will receive updates once the order is processed.</p>

  <a href="index.php" class="btn-modern">
    <i class="bi bi-house"></i> Back to Home
  </a>
</div>

<script>
  // Dark Mode Auto-Apply
  if(localStorage.getItem("dark") === "true")
      document.body.classList.add("dark-mode");

  // Confetti Burst Animation
  function shootConfetti() {
    const duration = 2 * 1000;
    const end = Date.now() + duration;

    (function frame() {
      confetti({
        particleCount: 6,
        angle: 60,
        spread: 55,
        origin: { x: 0 }
      });
      confetti({
        particleCount: 6,
        angle: 120,
        spread: 55,
        origin: { x: 1 }
      });

      if (Date.now() < end) requestAnimationFrame(frame);
    })();
  }

  // Fire when page loads
  shootConfetti();
</script>

</body>
</html>