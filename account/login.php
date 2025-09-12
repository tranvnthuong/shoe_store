<?php
session_start();
include("../configs/db.php");

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit;
}

// --- Khởi tạo biến lỗi để tránh Undefined variable ---
$error = "";

// Nếu có refresh_token trong cookie
if (isset($_COOKIE['refresh_token'])) {
  $token = $_COOKIE['refresh_token'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE refresh_token=? LIMIT 1");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if ($user) {
    $_SESSION['user_id']     = $user['id'];
    $_SESSION['email']       = $user['email'];
    $_SESSION['balance']     = $user['balance'];
    $_SESSION['role']        = $user['role'];
    $_SESSION['full_name']   = $user['full_name'];
    $_SESSION['day_of_birth']= $user['day_of_birth'];
    $_SESSION['phone']       = $user['phone'];
    $_SESSION['address']     = $user['address'];
    $_SESSION['created_at']  = $user['created_at'];

    setcookie("refresh_token", $token, time() + 60 * 60 * 24 * 7, "/", "", false, true);
    $stmt = $conn->prepare("UPDATE users SET refresh_token=? WHERE id=?");
    $stmt->bind_param("si", $token, $user['id']);
    $stmt->execute();
    header("Location: ../index.php");
    exit;
  } else {
    setcookie("refresh_token", "", -1, "/", "", false, true);
  }
}

// Xử lý khi POST login
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $email    = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  // ⚠️ Hiện tại so sánh plain-text. Nên nâng cấp thành password_hash() sau
  if ($user && $user['password'] == $password) {
    $_SESSION['user_id']     = $user['id'];
    $_SESSION['email']       = $user['email'];
    $_SESSION['balance']     = $user['balance'];
    $_SESSION['role']        = $user['role'];
    $_SESSION['full_name']   = $user['full_name'];
    $_SESSION['day_of_birth']= $user['day_of_birth'];
    $_SESSION['phone']       = $user['phone'];
    $_SESSION['address']     = $user['address'];
    $_SESSION['created_at']  = $user['created_at'];

    $token = bin2hex(random_bytes(32));
    setcookie("refresh_token", $token, time() + 60 * 60 * 24 * 7, "/", "", false, true);
    $stmt = $conn->prepare("UPDATE users SET refresh_token=? WHERE id=?");
    $stmt->bind_param("si", $token, $user['id']);
    $stmt->execute();

    header("Location: ../index.php");
    exit;
  } else {
    $error = "Sai email hoặc mật khẩu!";
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Đăng nhập - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
      overflow: hidden;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.92);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
      transform: translateY(40px);
      opacity: 0;
      animation: fadeUp 0.8s ease forwards;
    }

    @keyframes fadeUp {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .login-card h3 {
      font-weight: 700;
      background: linear-gradient(90deg, #4facfe, #00f2fe);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 8px rgba(102, 126, 234, 0.6);
    }

    .btn-gradient {
      background: linear-gradient(to right, #667eea, #764ba2);
      border: none;
      transition: all 0.3s ease;
    }

    .btn-gradient:hover {
      background: linear-gradient(to right, #764ba2, #667eea);
      transform: scale(1.03);
    }

    .toggle-active {
      color: #0d6efd !important;
    }
  </style>
</head>

<body>
  <?php include("../layout/header.php") ?>

  <div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card login-card shadow p-4" style="width:400px;">
      <h3 class="text-center mb-4">🔑 Đăng nhập</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
        </div>

        <div class="mb-3">
          <label class="form-label">Mật khẩu</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            <span class="input-group-text bg-white">
              <i class="fa fa-eye" id="togglePassword" style="cursor:pointer; color:#888;"></i>
            </span>
          </div>
        </div>

        <button class="btn btn-gradient w-100 py-2 text-white fw-bold">Đăng nhập</button>
        <p class="text-center mt-3 mb-0">
          Chưa có tài khoản? <a href="register.php" class="fw-semibold text-decoration-none">Đăng ký</a>
        </p>
      </form>
    </div>
  </div>

  <?php include("../layout/footer.php") ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle hiện/ẩn mật khẩu + đổi màu icon
    const togglePassword = document.querySelector("#togglePassword");
    const passwordInput = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      this.classList.toggle("fa-eye-slash");
      this.classList.toggle("toggle-active");
    });
  </script>
</body>

</html>
