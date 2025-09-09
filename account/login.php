<?php
session_start();
include("../configs/db.php");

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit;
}

if (isset($_COOKIE['refresh_token'])) {
  $token = $_COOKIE['refresh_token'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE refresh_token=? LIMIT 1");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
  if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['day_of_birth'] = $user['day_of_birth'];
    $_SESSION['phone'] = $user['phone'];

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

$error = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  // ⚠️ Hiện tại so sánh plain-text, bạn có thể nâng cấp thành password_hash sau
  if ($user && $user['password'] == $password) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['day_of_birth'] = $user['day_of_birth'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['created_at'] = $user['created_at'];

    $token = bin2hex(random_bytes(32));
    // Lưu vào cookie (httpOnly)
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
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
  <?php include("../layout/header.php") ?>
  <div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card shadow p-4" style="width:400px;">
      <h3 class="text-center">🔑 Đăng nhập</h3>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Mật khẩu</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Đăng nhập</button>
        <p class="text-center mt-3">Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
      </form>
    </div>
  </div>
  <?php include("../layout/footer.php") ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>