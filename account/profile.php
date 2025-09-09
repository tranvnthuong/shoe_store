<?php
session_start();
include("../configs/db.php");

// Lấy username từ session
$userId = $_SESSION['user_id'] ?? '';
if (!$userId) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin người dùng
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user1 = $result->fetch_assoc();

if (!$user1) {
    die("Người dùng không tồn tại!");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Thông tin cá nhân</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("../layout/header.php"); ?>

    <div class="container" style="padding-top: 80px;">
        <h2>Thông tin cá nhân</h2>
        <div class="card shadow-sm p-3 mb-4">
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($user1['full_name']); ?></p>
            <p><strong>Ngày sinh:</strong> <?= $user1['day_of_birth'] ?? "Chưa cập nhật"; ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user1['email']); ?></p>
            <p><strong>Số điện thoại:</strong> <?= $user1['phone'] ?? "Chưa cập nhật"; ?></p>
            <p><strong>Vai trò:</strong> <?= htmlspecialchars($user1['role']); ?></p>

            <!-- Nút chức năng -->
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
                <form method="post" action="profile_update.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateInfoModalLabel">Cập nhật thông tin cá nhân</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($user1['email']); ?>">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?= htmlspecialchars($user1['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="day_of_birth" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="day_of_birth" name="day_of_birth"
                                value="<?= $user1['day_of_birth'] ?? ""; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user1['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?= $user1['phone'] ?? ""; ?>" required>
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
                <form method="post" action="change_password.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($user1['email']); ?>">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>