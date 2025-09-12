<?php
session_start();
include("../configs/db.php");

// Nếu đã đăng nhập thì quay về
if (isset($_SESSION['username'])) {
  header("Location: ../index.php");
  exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $full_name    = trim($_POST['full_name']);
  $day_of_birth = $_POST['day_of_birth'];
  $email        = trim($_POST['email']);
  $password     = trim($_POST['password']);
  $confirm      = trim($_POST['confirm']);

  if ($password !== $confirm) {
    $error = "Mật khẩu nhập lại không khớp!";
  } else {
    // kiểm tra email tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result()->fetch_assoc();

    if ($check) {
      $error = "Email đã tồn tại!";
    } else {
      // hash mật khẩu để bảo mật
      $hashPassword = password_hash($password, PASSWORD_BCRYPT);

      // thêm user mới
      $stmt = $conn->prepare("INSERT INTO users (full_name, day_of_birth, email, password, role) VALUES (?, ?, ?, ?, 'user')");
      $stmt->bind_param("ssss", $full_name, $day_of_birth, $email, $hashPassword);

      if ($stmt->execute()) {
        $_SESSION['username']     = $email;
        $_SESSION['role']         = "user";
        $_SESSION['full_name']    = $full_name;
        $_SESSION['day_of_birth'] = $day_of_birth;
        header("Location: ../index.php");
        exit;
      } else {
        $error = "Có lỗi xảy ra, vui lòng thử lại!";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Đăng ký - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #00c6ff, #0072ff);
      overflow: hidden;
    }

    .register-card {
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

    .register-card h3 {
      font-weight: 700;
      background: linear-gradient(90deg, #36d1dc, #5b86e5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .form-control:focus {
      border-color: #0072ff;
      box-shadow: 0 0 8px rgba(0, 114, 255, 0.6);
    }

    .btn-gradient {
      background: linear-gradient(to right, #36d1dc, #5b86e5);
      border: none;
      transition: all 0.3s ease;
    }

    .btn-gradient:hover {
      background: linear-gradient(to right, #5b86e5, #36d1dc);
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
    <div class="card register-card shadow p-4" style="width:420px;">
      <h3 class="text-center mb-4">📝 Đăng ký</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
        </div>
        <div class="mb-3">
          <label>Họ và Tên</label>
          <input type="text" name="full_name" class="form-control" placeholder="Nhập họ tên..." required>
        </div>
        <div class="mb-3">
          <label>Ngày sinh</label>
          <input type="date" name="day_of_birth" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Mật khẩu</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            <span class="input-group-text bg-white">
              <i class="fa fa-eye" id="togglePassword" style="cursor:pointer; color:#888;"></i>
            </span>
          </div>
        </div>
        <div class="mb-3">
          <label>Nhập lại mật khẩu</label>
          <div class="input-group">
            <input type="password" name="confirm" id="confirm" class="form-control" placeholder="Xác nhận mật khẩu..." required>
            <span class="input-group-text bg-white">
              <i class="fa fa-eye" id="toggleConfirm" style="cursor:pointer; color:#888;"></i>
            </span>
          </div>
        </div>
        <button class="btn btn-gradient w-100 py-2 text-white fw-bold">Đăng ký</button>
        <p class="text-center mt-3 mb-0">Đã có tài khoản? <a href="login.php" class="fw-semibold text-decoration-none">Đăng nhập</a></p>
      </form>
    </div>
  </div>

  <?php include("../layout/footer.php") ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle hiển thị mật khẩu & xác nhận mật khẩu
    function setupToggle(toggleId, inputId) {
      const toggle = document.querySelector(toggleId);
      const input = document.querySelector(inputId);

      toggle.addEventListener("click", function () {
        const type = input.getAttribute("type") === "password" ? "text" : "password";
        input.setAttribute("type", type);
        this.classList.toggle("fa-eye-slash");
        this.classList.toggle("toggle-active");
      });
    }

    setupToggle("#togglePassword", "#password");
    setupToggle("#toggleConfirm", "#confirm");
  </script>
</body>

</html>
