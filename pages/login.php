<?php
session_start();
include("../configs/db.php");

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
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
        $_SESSION['username'] = $user['email'];
        $_SESSION['role'] = $user['role']; // admin hoặc user
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['day_of_birth'] = $user['day_of_birth'];
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
  <title>Đăng nhập</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <?php include("../layout/header.php") ?>
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
  <div class="card shadow p-4" style="width:400px;">
    <h3 class="text-center">🔑 Đăng nhập</h3>
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
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
</body>
</html>
