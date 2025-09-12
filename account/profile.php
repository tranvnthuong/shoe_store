<?php
session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì quay về login
$userId = $_SESSION['user_id'] ?? '';
if (!$userId) {
    header("Location: login.php");
    exit;
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = "";
$success = "";

/* ------------------- Cập nhật thông tin cá nhân ------------------- */
if (isset($_POST['update_profile'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) die("CSRF token không hợp lệ!");

    if (empty($_POST['full_name']) || trim($_POST['full_name']) == "") {
        $error = "Vui lòng nhập đầy đủ thông tin cá nhân!";
    } else {
        $full_name    = trim($_POST['full_name']);
        $day_of_birth = $_POST['day_of_birth'] ?? null;
        $phone        = trim($_POST['phone']);
        $address      = trim($_POST['address']);

        $sql = "UPDATE users SET full_name=?, day_of_birth=?, phone=?, address=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $day_of_birth, $phone, $address, $userId);
        $stmt->execute();

        $_SESSION['full_name']    = $full_name;
        $_SESSION['day_of_birth'] = $day_of_birth;
        $_SESSION['phone']        = $phone;
        $_SESSION['address']      = $address;

        $success = "Cập nhật thành công!";
    }
}

/* ------------------- Đổi mật khẩu ------------------- */
if (isset($_POST['change_password'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) die("CSRF token không hợp lệ!");

    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user2 = $result->fetch_assoc();

    if (!$user2 || !password_verify($current_password, $user2['password'])) {
        $error = "Mật khẩu hiện tại không chính xác!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Mật khẩu mới không khớp!";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed, $userId);
        $stmt->execute();

        $success = "Đổi mật khẩu thành công!";
    }
}

/* ------------------- Nạp tiền qua ngân hàng ------------------- */
if (isset($_POST['deposit_money'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) die("CSRF token không hợp lệ!");

    $amount = intval($_POST['amount']);
    if ($amount <= 0) {
        $error = "Số tiền nạp phải lớn hơn 0!";
    } else {
        $sql = "INSERT INTO nap_tien (user_id, so_tien, trang_thai) VALUES (?, ?, 'choduyet')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $amount);
        if ($stmt->execute()) {
            $success = "Bạn đã tạo yêu cầu nạp " . number_format($amount, 0, ',', '.') . " VND. 
                        Vui lòng chuyển khoản theo hướng dẫn và chờ admin duyệt!";
        } else {
            $error = "Có lỗi khi tạo yêu cầu nạp tiền!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Thông tin cá nhân</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../layout/header.php"); ?>

<div class="container" style="padding-top: 80px;">
  <h2><i class="fa-solid fa-address-card"></i>Thông tin cá nhân</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm p-3 mb-4">
    <p><strong>Họ tên:</strong> <?= $_SESSION['full_name']; ?></p>
    <p><strong>Ngày sinh:</strong> <?= $_SESSION['day_of_birth'] ?? "Chưa cập nhật"; ?></p>
    <p><strong>Email:</strong> <?= $_SESSION['email']; ?></p>
    <p><strong>Số dư:</strong> <?= number_format($_SESSION['balance'], 0, ',', '.') ?> VND</p>
    <p><strong>Số điện thoại:</strong> <?= $_SESSION['phone'] ?? "Chưa cập nhật"; ?></p>
    <p><strong>Địa chỉ:</strong> <?= $_SESSION['address'] ?? "Chưa cập nhật"; ?></p>
    <p><strong>Ngày tham gia:</strong> <?= $_SESSION['created_at'] ?? "Chưa cập nhật"; ?></p>
    <p><strong>Vai trò:</strong> <?= $_SESSION['role']; ?></p>
    
    <div class="d-flex justify-content-center gap-2">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateInfoModal"><i class="fa-solid fa-square-pen"></i>Cập nhật thông tin</button>
      <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="fa-solid fa-lock"></i>Đổi mật khẩu</button>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depositModal"><i class="fa-solid fa-wallet"></i>Nạp tiền</button>
      <a href="charge_history.php" class="btn btn-info"><i class="fa-solid fa-clock-rotate-left"></i>Lịch sử nạp</a>
      <a href="logout.php" class="btn btn-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i>Đăng xuất</a>
    </div>
  </div>
</div>

<!-- Modal 1: Cập nhật thông tin -->
<div class="modal fade" id="updateInfoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="update_profile" value="1">
        <div class="modal-header">
          <h5 class="modal-title">Cập nhật thông tin cá nhân</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label>Họ tên</label>
            <input type="text" class="form-control" name="full_name" value="<?= $_SESSION['full_name'] ?>" required>
          </div>
          <div class="mb-3"><label>Ngày sinh</label>
            <input type="date" class="form-control" name="day_of_birth" value="<?= $_SESSION['day_of_birth'] ?>">
          </div>
          <div class="mb-3"><label>Số điện thoại</label>
            <input type="text" class="form-control" name="phone" value="<?= $_SESSION['phone'] ?>">
          </div>
          <div class="mb-3"><label>Địa chỉ</label>
            <input type="text" class="form-control" name="address" value="<?= $_SESSION['address'] ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 2: Đổi mật khẩu -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="change_password" value="1">
        <div class="modal-header">
          <h5 class="modal-title">Đổi mật khẩu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label>Mật khẩu hiện tại</label>
            <input type="password" class="form-control" name="current_password" required>
          </div>
          <div class="mb-3"><label>Mật khẩu mới</label>
            <input type="password" class="form-control" name="new_password" required>
          </div>
          <div class="mb-3"><label>Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" name="confirm_password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 3: Nạp tiền qua ngân hàng -->
<div class="modal fade" id="depositModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="deposit_money" value="1">
        <div class="modal-header">
          <h5 class="modal-title">💰 Nạp tiền qua ngân hàng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <p><strong>Thông tin chuyển khoản:</strong></p>
            <p>Ngân hàng: <b>Vietcombank</b></p>
            <p>Số tài khoản: <b>0123456789</b></p>
            <p>Chủ tài khoản: <b>Nguyen Van A</b></p>
            <p>Nội dung: <b>naptien_<?= $_SESSION['email'] ?></b></p>
          </div>
          <div class="mb-3">
            <label for="amount">Số tiền muốn nạp (VND)</label>
            <input type="text" id="amount" class="form-control" name="amount" min="10000" step="1000" max="100000000" required autocomplete="off">
            <datalist id="suggestions">
              <option value="10,000 VND">
              <option value="100,000 VND">
              <option value="1,000,000 VND">
              <option value="10,000,000 VND">
              <option value="100,000,000 VND">
              <option value="20,000 VND">
              <option value="200,000 VND">
              <option value="2,000,000 VND">
              <option value="20,000,000 VND">
              <option value="30,000 VND">
              <option value="300,000 VND">
              <option value="3,000,000 VND">
              <option value="30,000,000 VND">
              <option value="40,000 VND">
              <option value="400,000 VND">
              <option value="4,000,000 VND">
              <option value="40,000,000 VND">
              <option value="50,000 VND">
              <option value="500,000 VND">
              <option value="5,000,000 VND">
              <option value="50,000,000 VND">
              <option value="60,000 VND">
              <option value="600,000 VND">
              <option value="6,000,000 VND">
              <option value="60,000,000 VND">
              <option value="70,000 VND">
              <option value="700,000 VND">
              <option value="7,000,000 VND">
              <option value="70,000,000 VND">
              <option value="80,000 VND">
              <option value="800,000 VND">
              <option value="8,000,000 VND">
              <option value="80,000,000 VND">
              <option value="90,000 VND">
              <option value="900,000 VND">
              <option value="9,000,000 VND">
              <option value="90,000,000 VND">
            </datalist>
            <script>
              const input = document.getElementById('amount');
              const options = Array.from(document.getElementById('suggestions').options).map(o => o.value);
              function parseMoney(str) {
                if (!str) return '';
                return str.replace(/[^\d]/g, ''); // bỏ hết ký tự không phải số
              }


              let raw = 0;
              input.addEventListener('input', () => {
                if (!input.hasAttribute("list")) {
                  input.setAttribute('list', 'suggestions');
                }
                if (input.value.trim() === '') {
                    input.removeAttribute('list');
                }
                if (options.includes(input.value)) {
                    raw = parseMoney(input.value).substring(0, 8);
                    input.value = raw;
                } else {
                    input.value = parseMoney(input.value).substring(0, 8);
                }
              });
              input.addEventListener('blur', () => {
                if (raw != 0)
                    input.value = raw;
                setTimeout(() => {
                  input.removeAttribute('list');
                  raw = 0;
                }, 1000);
              })
            </script>
          </div>
          <p class="text-muted"><i>Sau khi chuyển khoản, admin sẽ duyệt và cộng tiền vào tài khoản của bạn.</i></p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Tạo yêu cầu nạp</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include("../layout/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
