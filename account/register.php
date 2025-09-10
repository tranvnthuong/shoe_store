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
  $full_name = trim($_POST['full_name']);
  $day_of_birth = $_POST['day_of_birth'];
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm = trim($_POST['confirm']);

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
      // thêm user mới
      $stmt = $conn->prepare("INSERT INTO users (full_name, day_of_birth, email, password, role) VALUES (?, ?, ?, ?, 'user')");
      // password nên hash để bảo mật
      $stmt->bind_param("ssss", $full_name, $day_of_birth, $email, $password);
      if ($stmt->execute()) {
        $_SESSION['username'] = $email;
        $_SESSION['role'] = "user";
        $_SESSION['full_name'] = $full_name;
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
  <title>Đăng ký</title>
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
    <div class="card shadow p-4" style="width:420px;">
      <h3 class="text-center">📝 Đăng ký</h3>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Họ và Tên</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Ngày sinh</label>
          <input type="date" name="day_of_birth" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Mật khẩu</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Nhập lại mật khẩu</label>
          <input type="password" name="confirm" class="form-control" required>
        </div>
        <button class="btn btn-success w-100">Đăng ký</button>
        <p class="text-center mt-3">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
      </form>
    </div>
  </div>
  <?php include("../layout/footer.php") ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>