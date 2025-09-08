<?php
session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì quay về login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin user
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $day_of_birth = trim($_POST['day_of_birth']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE users SET day_of_birth=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("sssi", $day_of_birth, $phone, $address, $user_id);
    if ($stmt->execute()) {
        $msg = "Cập nhật thành công!";
    } else {
        $msg = "Có lỗi xảy ra!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Tài khoản của tôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <?php include("../layout/header.php") ?>
<div class="container my-5" style="max-width:600px;">
  <div class="card shadow p-4">
    <h3 class="mb-3">👤 Tài khoản của tôi</h3>
    <?php if($msg): ?><div class="alert alert-info"><?= $msg ?></div><?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="text" class="form-control" value="<?= $user['email'] ?>" disabled>
      </div>
      <div class="mb-3">
        <label>Ngày sinh</label>
        <input type="date" name="day_of_birth" value="<?= $user['day_of_birth'] ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Số điện thoại</label>
        <input type="text" name="phone" value="<?= $user['phone'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Địa chỉ</label>
        <textarea name="address" class="form-control"><?= $user['address'] ?? '' ?></textarea>
      </div>
      <button class="btn btn-success w-100">Cập nhật</button>
    </form>

    <a href="orders.php" class="btn btn-outline-primary w-100 mt-3">📦 Xem đơn hàng</a>
    <a href="../pages/logout.php" class="btn btn-outline-danger w-100 mt-2">🚪 Đăng xuất</a>
  </div>
</div>
  <?php include("../layout/footer.php") ?>
</body>
</html>
