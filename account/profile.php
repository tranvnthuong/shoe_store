<?php
session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì quay về login
$userId = $_SESSION['user_id'] ?? '';
if (!$userId) {
    header("Location: login.php");
    exit;
}

// CSRF Token tạo 1 lần theo session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = "";
$success = "";

// Cập nhật thông tin cá nhân
if (isset($_POST['update_profile'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token không hợp lệ!");
    }

    if (empty($_POST['full_name']) || trim($_POST['full_name']) == "") {
        $error = "Vui lông nhập đầy đủ thống tin cá nhân!";
    } else {

        $full_name    = $_POST['full_name'];
        $day_of_birth = $_POST['day_of_birth'] ?? null;
        $phone        = $_POST['phone'];
        $address      = $_POST['address'];

        $sql = "UPDATE users SET full_name = ?, day_of_birth = ?, phone = ?, address = ? WHERE id = ?";
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

// Đổi mật khẩu
if (isset($_POST['change_password'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token không hợp lệ!");
    }

    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user2 = $result->fetch_assoc();

    if ($user2['password'] !== $current_password) {
        $error = "Mật khẩu hiện tại không chính xác!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Mật khẩu mới không khớp nhau!";
    } else {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password, $userId);
        $stmt->execute();

        $success = "Đổi mật khẩu thành công!";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("../layout/header.php"); ?>

    <div class="container" style="padding-top: 80px;">
        <h2>Thông tin cá nhân</h2>

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
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#updateInfoModal">
                    Cập nhật thông tin
                </button>
                <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    Đổi mật khẩu
                </button>
                <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
            </div>
        </div>
    </div>

    <!-- Modal 1: Cập nhật thông tin -->
    <div class="modal fade" id="updateInfoModal" tabindex="-1" aria-labelledby="updateInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="modal-header">
                        <h5 class="modal-title" id="updateInfoModalLabel">Cập nhật thông tin cá nhân</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?= $_SESSION['full_name'] ?? ""; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="day_of_birth" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="day_of_birth" name="day_of_birth"
                                value="<?= $_SESSION['day_of_birth'] ?? ""; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?= $_SESSION['phone'] ?? ""; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="<?= $_SESSION['address'] ?? ""; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Đổi mật khẩu -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="change_password" value="1">

                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../layout/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>